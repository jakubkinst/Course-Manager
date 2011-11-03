<?php

/**
 * Base class for all application presenters.
 *
 * @author     Jakub Kinst
 * @package    Course-Manager
 */
abstract class BasePresenter extends MasterPresenter {

    /** @persistent Course ID */
    public $cid;    
    public $isTeacher;
    /** Boolean indicating user privileges */
    public $isStudent;
    /** Logical value indicating state of user */
    public $logged = false;

    /**
     * Initialization before rendering every presenter
     * Sends essential variables which are needed in every presenter to a template
     */
    protected function startup() {
        parent::startup();
        if ($this->getParam('cid') != null)
            $this->init($this->getParam('cid'));
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

    public function checkAuthorization() {
        if (!($this->isTeacher || $this->isStudent)) {
            $this->flashMessage('Unauthorized access.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }

    public function checkTeacherAuthority() {
        if (!$this->isTeacher) {
            $this->flashMessage('You must be a lector to add lesson.', $type = 'unauthorized');
            $this->redirect('course:homepage');
        }
    }

    public function checkLogged() {
        if (!$this->logged) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('CourseList:homepage');
        }
    }

    

}
