<?php

/**
 * Base class for all presenters, that are dependant on a particular course
 * Passes desired course-related variables to template.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
abstract class BaseCoursePresenter extends BasePresenter {

    /** Course ID */
    public $cid;

    /** Bollean indicating teacher privileges */
    public $isTeacher;

    /** Boolean indicating student privileges */
    public $isStudent;

    /** Boolean indicating state of user */
    public $logged = false;

    protected function startup() {
	parent::startup();
	if ($this->cid == null)
	    $this->cid = $this->getParam('cid');
	if ($this->cid != null) {
	    $this->init($this->cid);
	    $this->checkAuthorization();
	}
    }

    /**
     * Initialize variables for template
     * @param int $cid 
     */
    public function init($cid) {
	$this->cid = $cid;
	$uid = UserModel::getLoggedUser()->id;
	$this->isTeacher = CourseModel::isTeacher($uid, $this->cid);
	$this->isStudent = CourseModel::isStudent($uid, $this->cid);

	$this->template->isStudent = $this->isStudent;
	$this->template->isTeacher = $this->isTeacher;

	if ($this->isTeacher || $this->isStudent) {
	    $this->template->activeCourse = CourseModel::getCourse($this->cid);
	    $this->template->lessons = CourseModel::getLessons($this->cid);
	    $this->template->lectors = CourseModel::getTeachers($this->cid);
	    $this->template->students = CourseModel::getStudents($this->cid);
	}
    }

    /**
     * Security check for student/teacher authority
     */
    public function checkAuthorization() {
	if (!($this->isTeacher || $this->isStudent)) {
	    $this->flashMessage('Unauthorized access.', $type = 'unauthorized');
	    $this->redirect('courselist:homepage');
	}
    }

    /**
     * Security check for teacher authority
     */
    public function checkTeacherAuthority() {
	if (!$this->isTeacher) {
	    $this->flashMessage('You must be a lector to add lesson.', $type = 'unauthorized');
	    $this->redirect('course:homepage');
	}
    }

    /**
     * Security check for login state
     */
    public function checkLogged() {
	if (!$this->logged) {
	    $this->flashMessage('Please login.', $type = 'unauthorized');
	    $this->redirect('CourseList:homepage');
	}
    }

}
