<?php

/**
 * Description of UserPresenter
 *
 * @author JerRy
 */
class UserPresenter extends BasePresenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
        parent::startup();
    }

    public function actionLogout() {
        Environment::getUser()->logout();
        $this->redirect('Courselist:homepage');
    }

    protected function createComponentRegisterForm() {

        function myValidator($item) {
            $result = dibi::query('SELECT id FROM user WHERE email=%s', $item->getValue());
            return (count($result) === 0);
        }

        $form = new AppForm;
        $form->addText('email', 'E-mail:*')
                ->setEmptyValue('@')
                ->addRule(Form::EMAIL, 'Enter valid e-mail')
                ->addRule('myValidator', 'E-mail is already registered.');
        $form->addPassword('password', 'Password:*')
                ->addRule(Form::FILLED, 'Fill in the password')
                ->addRule(Form::MIN_LENGTH, 'Minimal password length is %d.', 5);
        $form->addPassword('password2', 'Verify password:*')
                ->addRule(Form::FILLED, 'Fill in the password again.')
                ->addRule(Form::EQUAL, 'Passwords don\'t match.', $form['password']);

        $form->addSubmit('send', 'Registrovat');
        $form->onSubmit[] = callback($this, 'registerFormSubmitted');
        return $form;
    }

    public function registerFormSubmitted($form) {
        $values = $form->getValues();
        unset($values['password2']);
        UserModel::addUser($values);
    }

}