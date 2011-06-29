<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

/**
 * Base class for all application presenters.
 *
 * @author     John Doe
 * @package    MyApplication
 */
abstract class BasePresenter extends Presenter {
    public $tCourses,$sCourses;
    public $logged = false;
            
    protected function beforeRender() {
        $user = Environment::getUser();
        $this->logged = $user->isLoggedIn();
        $this->template->logged = $this->logged;
        if ($this->logged) {
            $this->tCourses = CourseListModel::getTeacherCourses(Environment::getUser()->getIdentity());
            $this->sCourses = CourseListModel::getStudentCourses(Environment::getUser()->getIdentity());
            foreach ($this->tCourses as $course) {
                $course['lectors'] = CourseModel::getLectors($course['id']);
            }
            foreach ($this->sCourses as $course) {
                $course['lectors'] = CourseModel::getLectors($course['id']);
            }
            $this->template->tCourses = $this->tCourses;
            $this->template->sCourses = $this->sCourses;
            
            $this->template->user = $user->getIdentity();
            $this->template->userid = UserModel::getUserID($user->getIdentity());
        }
    }

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
