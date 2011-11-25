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
    /** @persistent */
    public $lang;
    public static $DEFAULT_LANG = 'en';
    public $translator;
    public $userSetLang;

    protected function startup() {
	parent::startup();

	// Relative Date helper
	function myDateHelper($value) {
	    return CommonModel::relative_date(strtotime($value));
	}

	$this->template->registerHelper('mydate', 'myDateHelper');

	// Texy helper
	$texy = new Texy;
	$this->template->registerHelper("texy", array($texy, "process"));


	$user = Environment::getUser();
	$this->logged = $user->isLoggedIn();
	$this->template->logged = $this->logged;
	if ($this->logged) {
	    $this->tCourses = CourseListModel::getTeacherCourses(UserModel::getLoggedUser()->id);
	    $this->sCourses = CourseListModel::getStudentCourses(UserModel::getLoggedUser()->id);


	    $this->template->tCourses = $this->tCourses;
	    $this->template->sCourses = $this->sCourses;

	    $this->template->user = UserModel::getLoggedUser();
	    $this->template->userid = $this->template->user->id;

	    $this->template->countUnread = MessageModel::countUnread();
	}
	if (!$this->logged & !$this->canbesignedout) {
	    $this->flashMessage('Please login.', $type = 'unauthorized');
	    $this->redirect('courselist:homepage');
	}

	if ($this->logged)
	    $this->userSetLang = SettingsModel::getMySettings()->lang;
	$this->decideLanguage();

	// template translator
	// uncomment to generate .po file
	//CommonModel::getTextExtract();
	$this->template->setTranslator($this->translator);
    }

    public function decideLanguage() {
	if ($this->getParam('lang') != '') {
	    $this->lang = $this->getParam('lang');
	    $this->setLanguage($this->lang);
	} else if (isset($this->userSetLang))
	    $this->setLanguage($this->userSetLang);
	else
	    $this->setLanguage(self::$DEFAULT_LANG);
    }

    public function setLanguage($lang) {
	if ($lang == 'en')
	    $this->translator = new GettextTranslator(APP_DIR . '/locale/en.mo');
	else if ($lang == 'cs')
	    $this->translator = new GettextTranslator(APP_DIR . '/locale/cs.mo');
    }

    /**
     * Form Factory - Sign In Form
     * @return AppForm 
     */
    protected function createComponentSignInForm() {

	$form = new AppForm;
	$form->setTranslator($this->translator);
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

    //WebLoader CSS
    protected function createComponentCss() {
	$css = new CssLoader;

	// cesta na disku ke zdroji
	$css->sourcePath = WWW_DIR . "/css";

	// cesta na webu ke zdroji (kvůli absolutizaci cest v css souboru)
	$css->sourceUri = Environment::getVariable("baseUri") . "css";

	// cesta na webu k cílovému adresáři
	$css->tempUri = Environment::getVariable("baseUri") . "webtemp";

	// cesta na disku k cílovému adresáři
	$css->tempPath = WWW_DIR . "/webtemp";

	return $css;
    }

// WebLoader JS
    protected function createComponentJs() {
	$js = new JavaScriptLoader;

	$js->tempUri = Environment::getVariable("baseUri") . "webtemp";
	$js->tempPath = WWW_DIR . "/webtemp";
	$js->sourcePath = WWW_DIR . "/js";

	return $js;
    }

}
