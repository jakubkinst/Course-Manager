<?php

/**
 * UserPresenter
 *
 * @author Jakub Kinst
 */
class UserPresenter extends MasterPresenter {

    public function startup() {
	$this->canbesignedout = true;
	parent::startup();
    }

    public function renderHomepage($uid) {
	$this->template->settings = SettingsModel::getSettings($uid);
	$this->template->showuser = UserModel::getUser($uid);
    }

    /**
     * Logout action
     */
    public function actionLogout() {
	Environment::getUser()->logout();
	$this->redirect('Courselist:homepage');
    }

    public function actionCheck($hash) {
	if (!UserModel::checkUser($hash))
	    throw new BadRequestException;
	else
	    $this->redirect('Courselist:homepage');
    }

    /**
     * Register template render
     */
    public function renderRegister() {
	
    }

    public function actionEdit() {
	$this['editForm']->setDefaults(UserModel::getLoggedUser());
    }

    /**
     * Form factory - Register user
     * @return AppForm 
     */
    protected function createComponentRegisterForm() {

	function myValidator($item) {
	    $result = dibi::query('SELECT id FROM user WHERE email=%s', $item->getValue());
	    return (count($result) === 0);
	}

	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('firstname', 'First name:*')
		->addRule(Form::FILLED, 'Fill the firstname.');
	$form->addText('lastname', 'Last name:*')
		->addRule(Form::FILLED, 'Fill the lastname.');
	$form->addText('email', 'E-mail:*')
		->setRequired()
		->setEmptyValue('@')
		->addRule(Form::EMAIL, 'Enter valid e-mail')
		->addRule('myValidator', 'E-mail is already registered.');
	$form->addPassword('password', 'Password:*')
		->addRule(Form::FILLED, 'Fill in the password')
		->addRule(Form::MIN_LENGTH, 'Minimal password length is %d.', 5);
	$form->addPassword('password2', 'Verify password:*')
		->addRule(Form::FILLED, 'Fill in the password again.')
		->addRule(Form::EQUAL, 'Passwords don\'t match.', $form['password']);
	$form->addText('web', 'Webpage:');

	$form->addSubmit('send', 'Register');
	$form->onSubmit[] = callback($this, 'registerFormSubmitted');
	return $form;
    }

    /**
     * Register user form handler
     * @param type $form 
     */
    public function registerFormSubmitted($form) {
	$values = $form->getValues();
	unset($values['password2']);
	if (UserModel::addUser($values)) {
	    $this->flashMessage('User ' . $values['email'] . ' registered. Please login.', $type = 'success');
	    $this->redirect('courselist:homepage');
	}
	else
	    $this->flashMessage('There was an error registering you.', $type = 'error');
    }

    protected function createComponentEditForm() {
	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('firstname', 'First name:*')
		->addRule(Form::FILLED, 'Fill the firstname.');
	$form->addText('lastname', 'Last name:*')
		->addRule(Form::FILLED, 'Fill the lastname.');
	$form->addText('web', 'Webpage:');

	$form->addSubmit('send', 'Register');
	$form->onSubmit[] = callback($this, 'editFormSubmitted');
	return $form;
    }

    public function editFormSubmitted($form) {
	$values = $form->getValues();
	if (UserModel::editUser($values)) {
	    $this->flashMessage('User data successfully edited.', $type = 'success');
	    $this->redirect('courselist:homepage');
	}
	else
	    $this->flashMessage('There was an error editting your user data.', $type = 'error');
    }

}