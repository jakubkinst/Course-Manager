<?php

/**
 * Presenter dedicated to User profile module
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class UserPresenter extends BasePresenter {
	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	public function startup() {
		$this->canbesignedout = true;
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * User homepage - profile page
	 */
	public function renderHomepage() {
		$uid = $this->getParam('uid');
		if ($uid == null)
			$uid = UserModel::getLoggedUser()->id;
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

	/**
	 * Confirms user email address
	 * @param string $hash Hash from confirmation email
	 */
	public function actionCheck($hash) {
		if (!UserModel::checkUser($hash))
			throw new BadRequestException;
		else {
			$this->flashMessage('User confirmed. Now you can login.','success');
			$this->redirect('Courselist:homepage');
		}
	}

	/**
	 * Edit user info action
	 */
	public function actionEdit() {
		$this['editForm']->setDefaults(UserModel::getLoggedUser());
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Register user form
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
				->addRule(Form::MIN_LENGTH, 'Minimal password length is 5', 5);
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
	 * @param AppForm $form
	 */
	public function registerFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		unset($values['password2']);
		$baseUri = $this->getHttpRequest()->getUri()->getBaseUri();
		if (UserModel::addUser($values, $baseUri)) {
			$this->flashMessage('User ' . $values['email'] . ' registered. Email with next steps was sent to your e-mail address.', $type = 'success');
			$this->redirect('courselist:homepage');
		}
		else
			$this->flashMessage('There was an error registering you.', $type = 'error');
	}

	/**
	 * Edit profile form
	 * @return AppForm
	 */
	protected function createComponentEditForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('firstname', 'First name:*')
				->addRule(Form::FILLED, 'Fill the firstname.');
		$form->addText('lastname', 'Last name:*')
				->addRule(Form::FILLED, 'Fill the lastname.');
		$form->addText('web', 'Webpage:');

		$form->addSubmit('send', 'Save');
		$form->onSubmit[] = callback($this, 'editFormSubmitted');
		return $form;
	}

	/**
	 * Edit user form handler
	 * @param AppForm $form
	 */
	public function editFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (UserModel::editUser($values)) {
			$this->flashMessage('User data successfully edited.', $type = 'success');
			$this->redirect('courselist:homepage');
		}
		else
			$this->flashMessage('There was an error editting your user data.', $type = 'error');
	}

}