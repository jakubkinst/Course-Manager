<?php

/**
 * Course presenter.
 *
 */
class CoursePresenter extends BasePresenter {

    
    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->init($cid);
        $this->checkAuthorization();
    }

    /**
     * Add lesson temlate render
     * @param type $cid 
     */
    public function renderAddLesson($cid) {
        $this->init($cid);
        
        // if not teacher, redirect to homepage
        $this->checkTeacherAuthority();
    }

    /**
     * Adding course template render
     */
    public function renderAdd() {
        $this->checkLogged();
    }

    /**
     * Form Factory - Add Course Form
     * @return AppForm 
     */
    protected function createComponentAddForm() {
        $form = new AppForm;
        $form->addText('name', 'Course name:*')
                ->addRule(Form::FILLED, 'Set course name.');
        $form->addTextArea('description', 'Course description:');

        $form->addSubmit('send', 'Create course');
        $form->onSubmit[] = callback($this, 'addFormSubmitted');

        return $form;
    }

    /**
     * Add Course Form Handler
     * @param type $form 
     */
    public function addFormSubmitted($form) {
        $values = $form->getValues();
        $user = Environment::getUser()->getIdentity();
        CourseModel::addCourse($user, $values);
        $this->flashMessage('Course created', $type = 'success');
        $this->redirect('courselist:homepage');
    }

    /**
     * Form factory - Add lesson form
     * @return AppForm 
     */
    protected function createComponentAddLessonForm() {
        $form = new AppForm;
        $form->addText('topic', 'Topic:*')
                ->addRule(Form::FILLED, 'Set lesson topic.');
        $form->addTextArea('description', 'Lesson description:');

        $form->addSubmit('send', 'Add lesson');
        $form->onSubmit[] = callback($this, 'addLessonFormSubmitted');
        return $form;
    }

    /**
     * Add Lesson Form Handler
     * @param type $form 
     */
    public function addLessonFormSubmitted($form) {
        $values = $form->getValues();
        $values['date'] = new DateTime;
        $values['Course_id'] = $this->cid;
        CourseModel::addLesson($values);

        $this->flashMessage('Lesson added', $type = 'success');
        $this->redirect('course:homepage', $values['Course_id']);
    }

    /**
     * Form factory - Invite student Form
     * @return AppForm 
     */
    protected function createComponentInviteStudentForm() {
        $form = new AppForm;
        $form->addText('email', 'E-mail:*')
                ->addRule(Form::FILLED, 'Fill email.')
                ->addRule(Form::EMAIL, 'Wrong e-mail format');

        $form->addSubmit('invite', 'Invite to' . $this->cid);
        $form->onSubmit[] = callback($this, 'inviteStudentFormSubmitted');
        $form->addHidden('Course_id', $this->cid);
        return $form;
    }

    /**
     * Invite Student form handler
     * @param type $form 
     */
    public function inviteStudentFormSubmitted($form) {
        $values = $form->getValues();
        $values['date'] = new DateTime;
        CourseModel::addStudent($values);
        $this->flashMessage('Student invited', $type = 'success');
    }

}
