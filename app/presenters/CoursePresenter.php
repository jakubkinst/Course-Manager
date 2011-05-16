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

    public function renderAdd() {
        
    }

    private function getCourses() {
        
    }

    protected function createComponentAddForm() {
        $form = new AppForm;
        $form->addText('name', 'Course name:*')
                ->addRule(Form::FILLED, 'Set course name.');
        $form->addTextArea('description', 'Course description:*')
                ->addRule(Form::FILLED, 'Fill in the password');

        $form->addSubmit('send', 'Create course');
        $form->onSubmit[] = callback($this, 'addFormSubmitted');
        
        return $form;
    }
    public function addFormSubmitted($form){
        $values = $form->getValues();
        CourseModel::addCourse($values);
        $this->redirect('courselist:default');
        
    }

}
