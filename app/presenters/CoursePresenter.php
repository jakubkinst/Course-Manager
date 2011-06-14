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
    
    /** @persistent */ public $courseid;
    public $isTeacher;
    public $isStudent;
    public function actionHomepage($courseid){
        $this->courseid = $courseid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(),$this->courseid);
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(),$this->courseid);
           
        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;
        
        if ($this->isTeacher || $this->isStudent){
            $this->template->course = CourseModel::getCourseByID($this->courseid);
            $this->template->lessons = CourseModel::getLessons($this->courseid);            
        }
    }
    public function actionAddLesson(){
        $this->isTeacher = CourseModel::approvedUser(Environment::getUser()->getIdentity(),$this->courseid);
        if (!$this->isTeacher)
                $this->redirect('CourseList:homepage');
    }


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
    
    protected function createComponentInviteStudentForm() {
        $form = new AppForm;
        $form->addText('email', 'E-mail:*')
                ->addRule(Form::FILLED, 'Fill email.')
                ->addRule(Form::EMAIL,'Wrong e-mail format');

        $form->addSubmit('invite', 'Invite to'.$this->courseid);
        $form->onSubmit[] = callback($this, 'inviteStudentFormSubmitted');
        $form->addHidden('Course_id',$this->courseid);
        return $form;
    }
    public function inviteStudentFormSubmitted($form){
        $values = $form->getValues();
        $values['date'] = new DateTime;
        CourseModel::addStudent($values);       
    }

}
