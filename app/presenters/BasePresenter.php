<?php

/**
 * Base class for application presenters. Defines variables and objects that
 * are common for all application presenters. Contains Sign-In form which is included
 * in layout.latte. Passes some neccessary variables to every single template
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
abstract class BasePresenter extends Presenter {

    /**
     * @persistent
     * True if accessing from Android App
     * @var boolean
     */
    public $mobile;

    /** Teachered courses */
    public $tCourses;

    /** Courses where acting as student */
    public $sCourses;

    /** Boolean indicating user privileges */

    /** Is this presenter for unauthorised users too ? */
    public $canbesignedout = false;

    /** Logical value indicating state of user */
    public $logged = false;

    /** @persistent language variable - if set, stays persistent */
    public $lang;

    /** User preferred language - may not be set */
    public $userSetLang;

    /** Default language value - English */
    public static $DEFAULT_LANG = 'en';

    /**
     * GetTextTranslator instance responsible for translating strings all
     * across the application
     */
    public $translator;

    /**
     * Paginator instance for VisualPaginator
     * Page control is available in every single presenter/template
     */
    public $paginator;

    // Relative Date helper
    function dateHelper($value) {
        return CommonModel::relative_date(strtotime($value));
    }

    // Relative Date with time helper
    function myDateTimeHelper($value) {
        return CommonModel::relative_date(strtotime($value)) . ' ' . date('H:i', strtotime($value));
    }

    /**
     * Startup function
     * Called first when Presenter is created
     * Registers helpers
     */
    protected function startup() {
        parent::startup();
        if ($this->mobile == null)
            $this->mobile = $this->getParam('mobile');
        $this->template->registerHelper('mydate', callback($this, 'dateHelper'));
        $this->template->registerHelper('mydatetime', callback($this, 'myDateTimeHelper'));

        // Texy helper
        $texy = new Texy;
        $this->template->registerHelper("texy", array($texy, "process"));

        // Set neccessary variables and pass them to template
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
        // Handle unathorized access
        if (!$this->logged & !$this->canbesignedout) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }

        // language settings
        // first try to get user-preferred language
        if ($this->logged)
            $this->userSetLang = SettingsModel::getMySettings()->lang;
        $this->decideLanguage();

        // template translator
        // uncomment to generate .po file
        // CommonModel::getTextExtract();
        $this->template->setTranslator($this->translator);

        // paging control - create Paginator instance
        $pages = new VisualPaginator($this, 'pages');
        $this->paginator = $pages->getPaginator();
    }

    /**
     * For mobile
     */
    protected function afterRender() {
        parent::afterRender();
        if ($this->mobile) {
            ob_start();
            $files = $this->formatTemplateFiles($this->getName(), $this->view);
            foreach ($files as $file) {
                if (is_file($file)) {
                    $this->template->setFile($file);
                    break;
                }
            }
            $this->template->render();
            ob_end_clean();
            $resp = new TemplateParametersResponse($this);
            $this->sendResponse(new TemplateParametersResponse($this));
        }
    }

    /**
     * Gets language based on user settings or HTTP parameter
     */
    public function decideLanguage() {
        if ($this->getParam('lang') != '') {
            $this->lang = $this->getParam('lang');
            $this->setLanguage($this->lang);
        } else if (isset($this->userSetLang))
            $this->setLanguage($this->userSetLang);
        else
            $this->setLanguage(self::$DEFAULT_LANG);
    }

    /**
     * Create and set Translator based on desired language
     * @param string $lang
     */
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
     * @param Form $form
     */
    public function signInFormSubmitted(AppForm $form) {
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
            $this->flashMessage($e->getMessage(), 'error');
        }
    }

    /**
     * Loads Webloader extension for better CSS file loading
     * @return CssLoader
     */
    protected function createComponentCss() {
        $css = new CssLoader;
        $css->sourcePath = WWW_DIR . "/css";
        $css->sourceUri = Environment::getVariable("baseUri") . "css";
        $css->tempUri = Environment::getVariable("baseUri") . "webtemp";
        $css->tempPath = WWW_DIR . "/webtemp";
        return $css;
    }

    /**
     * Loads Webloader extension for better JS file loading
     * @return JavaScriptLoader
     */
    protected function createComponentJs() {
        $js = new JavaScriptLoader;

        $js->tempUri = Environment::getVariable("baseUri") . "webtemp";
        $js->tempPath = WWW_DIR . "/webtemp";
        $js->sourcePath = WWW_DIR . "/js";
        return $js;
    }

    /**
     * Function used to declare and pass PHP variables to JavaScript
     * external files
     * @return string
     */
    public function getJSVariables() {
        $vars = '
		delete_confirm_message = "' . _('Are you sure you want to delete this item') . '";
		texyla_preview_link = "' . @$this->link('Texyla:preview') . '";
		texyla_base = "' . BASE_DIR . '/document_root/texyla/";
		active_course_id = "' . @$this->template->activeCourse->id . '";
		choose_anwsers_message = "' . _('Choose anwsers') . '";
	';
        return $vars;
    }

}
