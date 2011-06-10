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
    
    public $courseid;
    public $approved;
    public function actionHomepage($cid){
        $this->courseid = $cid;
        $this->approved = CourseModel::approvedUser(Environment::getUser()->getIdentity(),$cid);
           
        
        $this->template->approved = $this->approved;
        if ($this->approved){
            $this->template->course = CourseModel::getCourseByID($cid);
            $this->template->lessons = CourseModel::getLessons($cid);            
        }
    }
    public function actionAddLesson($cid){
        $this->approved = CourseModel::approvedUser(Environment::getUser()->getIdentity(),$cid);
        if (!$this->approved)
                $this->redirect('CourseList:homepage');
        $this->courseid = $cid;
    }


    public function renderHomepage($cid) {
       
       
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
    
    
    protected function createComponentAddLessonForm() {
        $form = new AppForm;
        $form->addText('topic', 'Topic:*')
                ->addRule(Form::FILLED, 'Set lesson topic.');
        $form->addTextArea('description', 'Lesson description:');

        $form->addSubmit('send', 'Add lesson');
        $form->onSubmit[] = callback($this, 'addLessonFormSubmitted');
        $form->addHidden('Course_id',$this->courseid);
        return $form;
    }
    public function addLessonFormSubmitted($form){
        $values = $form->getValues();
        $values['date'] = new DateTime;
        CourseModel::addLesson($values);
        $this->redirect('course:homepage',$values['Course_id'] );        
    }
    

}
