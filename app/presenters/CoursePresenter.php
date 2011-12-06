<?php

/**
 * Course presenter.
 *
 */
class CoursePresenter extends BaseCoursePresenter {

    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
	$this->checkAuthorization();
    }

    public function renderInviteStudent() {
	$this->template->invites = CourseListModel::getInvites($this->cid);
    }

    /**
     * Add lesson temlate render
     * @param type $cid 
     */
    public function renderAddLesson($cid) {
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
	$form->setTranslator($this->translator);
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

    public function renderEdit($cid) {
	$this->checkTeacherAuthority();
	$this['editForm']->setDefaults(CourseModel::getCourseByID($cid));
    }

    public function handleDelete($cid) {
	$this->checkTeacherAuthority();
	if (CourseModel::deleteCourse($cid)) {
	    $this->flashMessage(_('Course Deleted'), 'success');
	    $this->redirect('courselist:homepage');
	}
	else
	    $this->flashMessage(_('There was an error deleting the course'), 'error');
    }

    protected function createComponentEditForm() {
	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('name', 'Course name:*')
		->addRule(Form::FILLED, 'Set course name.');
	$form->addTextArea('description', 'Course description:');
	$form->addSubmit('send', 'Save');
	$form->onSubmit[] = callback($this, 'editFormSubmitted');

	return $form;
    }

    public function editFormSubmitted($form) {
	$values = $form->getValues();
	if (CourseModel::editCourse($this->cid, $values)) {
	    $this->flashMessage('Course edited.', $type = 'success');
	    $this->redirect('homepage');
	}
	else
	    $this->flashMessage('There was an error editting the Course.', $type = 'error');
    }

    /**
     * Form factory - Add lesson form
     * @return AppForm 
     */
    protected function createComponentAddLessonForm() {
	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('topic', 'Topic:*')
		->addRule(Form::FILLED, 'Set lesson topic.');
	$form->addTextArea('description', 'Lesson description:')->getControlPrototype()->class = "texyla";
	$form->addText('date', 'Date:')->setRequired()->getControlPrototype()->class = "datepicker";
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
	CourseModel::addLesson($values, $this->cid);

	$this->flashMessage('Lesson added', $type = 'success');
	$this->redirect('course:homepage', $values['Course_id']);
    }

    /**
     * Form factory - Invite student Form
     * @return AppForm 
     */
    protected function createComponentInviteStudentForm() {
	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addText('email', 'E-mail:*')
		->addRule(Form::FILLED, 'Fill email.')
		->addRule(Form::EMAIL, 'Wrong e-mail format');

	$form->addSubmit('invite', 'Invite');
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
	if (CourseModel::inviteStudent($values))
	    $this->flashMessage('Student invited', $type = 'success');
	else
	    $this->flashMessage('There was a problem inviting this student', $type = 'error');
    }

}
