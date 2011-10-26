<?php

/**
 * Base class for all application presenters.
 *
 * @author     Jakub Kinst
 * @package    Course-Manager
 */
abstract class BasePresenter extends Presenter {

    
    /** @persistent Course ID */
    public $cid;
    
    /** Teachered courses */
    public $tCourses;
    /** Courses where acting as student */
    public $sCourses;
    
    /** Boolean indicating user privileges */
    public $isTeacher;
    
    /** Boolean indicating user privileges */
    public $isStudent;
    
    /** Logical value indicating state of user */
    public $logged = false;
    
    /** Is this presenter for unauthorised users too ? */
    public $canbesignedout = false;

    /**
     * Initialization before rendering every presenter
     * Sends essential variables which are needed in every presenter to a template
     */
    protected function beforeRender() {
        $user = Environment::getUser();
        $this->logged = $user->isLoggedIn();
        $this->template->logged = $this->logged;
        if ($this->logged) {
            $this->tCourses = CourseListModel::getTeacherCourses(Environment::getUser()->getIdentity());
            $this->sCourses = CourseListModel::getStudentCourses(Environment::getUser()->getIdentity());

            // adds list of teachers to course objects
            foreach ($this->tCourses as $course) {
                $course['lectors'] = CourseModel::getLectors($course['id']);
            }

            // adds list of teachers to course objects
            foreach ($this->sCourses as $course) {
                $course['lectors'] = CourseModel::getLectors($course['id']);
            }

            $this->template->tCourses = $this->tCourses;
            $this->template->sCourses = $this->sCourses;

            $this->template->user = $user->getIdentity();
            $this->template->userid = UserModel::getUserID($user->getIdentity());
        }
        if (!$this->logged && !$this->canbesignedout) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }
    
    /**
     * Initialize variables for template
     * @param type $cid 
     */
    public function init($cid) {
        $this->cid = $cid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(), $this->cid);
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(), $this->cid);

        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;

        if ($this->isTeacher || $this->isStudent) {
            $this->template->course = CourseModel::getCourseByID($this->cid);
            $this->template->lessons = CourseModel::getLessons($this->cid);
            $this->template->lectors = CourseModel::getLectors($this->cid);
            $this->template->students = CourseModel::getStudents($this->cid);
        }
    }
    
    public function checkAuthorization(){
        if (!($this->isTeacher || $this->isStudent)) {
            $this->flashMessage('Unauthorized access.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }
    
    public function checkTeacherAuthority(){
        if (!$this->isTeacher) {
            $this->flashMessage('You must be a lector to add lesson.', $type = 'unauthorized');
            $this->redirect('course:homepage');
        }
    }
    
    public function checkLogged(){
        if (!$this->logged) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('CourseList:homepage');
        }
    }

    /**
     * Form Factory - Sign In Form
     * @return AppForm 
     */
    protected function createComponentSignInForm() {

        $form = new AppForm;
        $form->addText('email', 'E-Mail:')
                ->setRequired('Please provide an e-mail.');

        $form->addPassword('password', 'Password:')
                ->setRequired('Please provide a password.');

        $form->addCheckbox('remember', 'Remember');

        $form->addSubmit('send', 'Sign in');

        $form->onSubmit[] = callback($this, 'signInFormSubmitted');
        return $form;
    }

    /**
     * Sign In Form Handler
     * @param type $form 
     */
    public function signInFormSubmitted($form) {
        try {
            $values = $form->getValues();
            if ($values->remember) {
                $this->getUser()->setExpiration('+ 14 days', FALSE);
            } else {
                $this->getUser()->setExpiration('+ 20 minutes', TRUE);
            }
            $this->getUser()->login($values->email, $values->password);
            $this->redirect('Courselist:homepage');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
