<?php

/**
 * Presenter dedicated to Course homepage and course-related actions
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class CoursePresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Course homepage
	 * @param int $cid Course ID
	 */
	public function renderHomepage($cid) {
		$this->checkAuthorization();
		$this->template->events = EventModel::getUpcomingEvents($this->cid, 10);
	}

	/**
	 * Invite students
	 * @param int $cid Course ID
	 */
	public function renderInviteStudent($cid) {
		$this->template->invites = CourseListModel::getInvites($this->cid);
	}

	/**
	 * Adding new lesson
	 * @param int $cid Course ID
	 */
	public function renderAddLesson($cid) {
		// if not teacher, redirect to homepage
		$this->checkTeacherAuthority();
	}

	/**
	 * Adding new course
	 */
	public function renderAdd() {
		$this->checkLogged();
	}

	/**
	 * Editting Course
	 */
	public function renderEdit($cid) {
		$this->checkTeacherAuthority();
		$this['editForm']->setDefaults(CourseModel::getCourse($cid));
	}

	/*
	 * =============================================================
	 * ==================  Signal Handlers =========================
	 */

	/**
	 * Promotes a student to a teacher
	 * @param int $uid UserID
	 */
	public function handleMakeTeacher($uid) {
		$this->checkTeacherAuthority();
		CourseModel::makeTeacher($this->cid, $uid);
	}

	/**
	 * Delete course handler
	 */
	public function handleDelete($cid) {
		$this->checkTeacherAuthority();
		if (CourseModel::deleteCourse($cid)) {
			$this->flashMessage(_('Course Deleted'), 'success');
			$this->redirect('courselist:homepage');
		}
		else
			$this->flashMessage(_('There was an error deleting the course'), 'error');
	}

	/**
	 * User leaves course
	 */
	public function handleLeave() {
		if (CourseModel::leaveCourse($this->cid)) {
			$this->flashMessage(_('You successfully left the course.'), 'success');
			$this->redirect('courselist:homepage');
		}
		else
			$this->flashMessage(_('There was an error leaving the course'), 'error');
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Add New Course Form
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
	 * @param AppForm $form
	 */
	public function addFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		CourseModel::addCourse($values);
		$this->flashMessage('Course created', $type = 'success');
		$this->redirect('courselist:homepage');
	}

	/**
	 * Edit Course Form
	 * @return AppForm
	 */
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

	/**
	 * Edit Form handler
	 * @param AppForm $form
	 */
	public function editFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (CourseModel::editCourse($this->cid, $values)) {
			$this->flashMessage('Course edited.', $type = 'success');
			$this->redirect('homepage',$this->cid);
		}
		else
			$this->flashMessage('There was an error editting the Course.', $type = 'error');
	}

	/**
	 * Add lesson Form
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
		$this->redirect('course:homepage', $this->cid);
	}

	/**
	 * Invite student Form
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
		return $form;
	}

	/**
	 * Invite Student form handler
	 * @param AppForm $form
	 */
	public function inviteStudentFormSubmitted($form) {
		$values = $form->getValues();
		$values['date'] = new DateTime;
		if (CourseModel::inviteStudent($values, $this->cid))
			$this->flashMessage('Student invited', $type = 'success');
		else
			$this->flashMessage('There was a problem inviting this student', $type = 'error');
	}

}
