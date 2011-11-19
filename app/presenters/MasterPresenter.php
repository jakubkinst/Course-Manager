<?php

/**
 * Base class for all application presenters.
 *
 * @author     Jakub Kinst
 * @package    Course-Manager
 */
abstract class MasterPresenter extends Presenter {

    /** Teachered courses */
    public $tCourses;
    /** Courses where acting as student */
    public $sCourses;
    /** Boolean indicating user privileges */
    /** Is this presenter for unauthorised users too ? */
    public $canbesignedout = false;
    /** Logical value indicating state of user */
    public $logged = false;

    protected function startup() {
	parent::startup();
	
	function myDateHelper($value) {
	    return CommonModel::relative_date(strtotime($value));
	}
	$this->template->registerHelper('mydate', 'myDateHelper');
	$user = Environment::getUser();
	$this->logged = $user->isLoggedIn();
	$this->template->logged = $this->logged;
	if ($this->logged) {
	    $this->tCourses = CourseListModel::getTeacherCourses(Environment::getUser()->getIdentity());
	    $this->sCourses = CourseListModel::getStudentCourses(Environment::getUser()->getIdentity());


	    $this->template->tCourses = $this->tCourses;
	    $this->template->sCourses = $this->sCourses;

	    $this->template->user = UserModel::getLoggedUser();
	    $this->template->userid = UserModel::getUserID($user->getIdentity());

	    $this->template->countUnread = MessageModel::countUnread();
	}
	if (!$this->logged & !$this->canbesignedout) {
	    $this->flashMessage('Please login.', $type = 'unauthorized');
	    $this->redirect('courselist:homepage');
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
