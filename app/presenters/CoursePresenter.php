<?php

/**
 * Course presenter.
 *
 */
class CoursePresenter extends BasePresenter {

    /** @persistent Course ID */
    public $cid;
    /** Boolean indicating user privileges */
    public $isTeacher;
    
    /** Boolean indicating user privileges */
    public $isStudent;

    /**
     * Initialize variables for template
     * @param type $cid 
     */
    public function init($cid) {
        $this->cid = $cid;
        $this->isTeacher = CourseModel::isTeacher(Environment::getUser()->getIdentity(), $this->cid);
        $this->isStudent = CourseModel::isStudent(Environment::getUser()->getIdentity(), $this->cid);

        $this->template->isStudent = $this->isStudent;
        $this->template->isTeacher = $this->isTeacher;

        if ($this->isTeacher || $this->isStudent) {
            $this->template->course = CourseModel::getCourseByID($this->cid);
            $this->template->lessons = CourseModel::getLessons($this->cid);
            $this->template->lectors = CourseModel::getLectors($this->cid);
            $this->template->students = CourseModel::getStudents($this->cid);
        }
    }

    /**
     * Before Render security check
     */
    public function beforeRender() {
        parent::beforeRender();
        if (!$this->logged) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }

    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->init($cid);
        if (!($this->isTeacher || $this->isStudent)) {
            $this->flashMessage('Unauthorized access.', $type = 'unauthorized');
            $this->redirect('courselist:homepage');
        }
    }

    /**
     * Add lesson temlate render
     * @param type $cid 
     */
    public function renderAddLesson($cid) {
        $this->init($cid);
        // if not teacher, redirect to homepage
        if (!$this->isTeacher) {
            $this->flashMessage('You must be a lector to add lesson.', $type = 'unauthorized');
            $this->redirect('course:homepage');
        }
    }

    /**
     * Adding course template render
     */
    public function renderAdd() {

        if (!$this->logged) {
            $this->flashMessage('Please login.', $type = 'unauthorized');
            $this->redirect('CourseList:homepage');
        }
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
