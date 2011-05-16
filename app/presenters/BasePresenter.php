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

    protected function beforeRender() {
        $user = Environment::getUser();        
        if ($user->isLoggedIn()) {
            $this->template->courses = CourseListModel::getCourses(Environment::getUser()->getIdentity());
            $this->template->logged = true;
            $this->template->user = $user->getIdentity();
        }
        else
            $this->template->logged = false;
    }

    protected function createComponentSignInForm() {

        $form = new AppForm;
        $form->addText('username', 'E-Mail:')
                ->setRequired('Please provide an e-mail.');

        $form->addPassword('password', 'Password:')
                ->setRequired('Please provide a password.');

        $form->addCheckbox('remember', 'Remember me on this computer');

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
            $this->getUser()->login($values->username, $values->password);
            $this->redirect('Courselist:homepage');
        } catch (AuthenticationException $e) {
            $form->addError($e->getMessage());
        }
    }

}
