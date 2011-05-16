<?php

/**
 * My Application
 *
 * @copyright  Copyright (c) 2010 John Doe
 * @package    MyApplication
 */

/**
 * Course presenter.
 *
 */
class CoursePresenter extends BasePresenter {

    public function renderHomepage() {
        
    }

    public function actionAdd() {
        if (!Environment::getUser()->isLoggedIn()){
            $this->redirect('CourseList:homepage');            
        }
    }

    protected function createComponentAddForm() {
        $form = new AppForm;
        $form->addText('name', 'Course name:*')
                ->addRule(Form::FILLED, 'Set course name.');
        $form->addTextArea('description', 'Course description:');

        $form->addSubmit('send', 'Create course');
        $form->onSubmit[] = callback($this, 'addFormSubmitted');
        
        return $form;
    }
    public function addFormSubmitted($form){
        $values = $form->getValues();
        $user = Environment::getUser()->getIdentity();
        CourseModel::addCourse($user,$values);
        $this->redirect('courselist:homepage');
        
    }

}
