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
    
    public function actionHomepage(){
    }


    public function renderHomepage() {
       $courseid = $this->getParam('id');
        if (CourseModel::approvedUser(Environment::getUser()->getIdentity(),$courseid)){
            $this->template->approved = true;
            $this->template->course = CourseModel::getCourseByID($courseid);
        }
        else $this->template->approved = false;
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
