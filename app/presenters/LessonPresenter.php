<?php

/**
 * Presenter dedicated to Lesson homepage and lesson-related actions
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class LessonPresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * ==================     Variables    =========================
	 */

	/**
	 * @var int Lesson ID
	 */
	public $lid;

	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	protected function startup() {
		if (null != $this->getParam('lid')) {
			$this->lid = $this->getParam('lid');
			$this->cid = CourseModel::getCourseIDByLessonID($this->lid);
		}
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Lesson Homepage
	 * @param int $lid Lesson ID
	 */
	public function renderHomepage($lid) {

		$this->paginator->itemsPerPage = 10;
		$this->paginator->itemCount = CourseModel::countComments($this->lid);

		$this->template->lesson = CourseModel::getLessonByID($this->lid);
		$this->template->resources = ResourceModel::getLessonFiles($this->lid);
		$this->template->comments = CourseModel::getComments($this->lid, $this->paginator->offset, $this->paginator->itemsPerPage);
	}

	/**
	 * Add resource to a lesson
	 * @param int $lid Lesson ID
	 */
	public function actionAddResource($lid) {
		$this->checkTeacherAuthority();
		$uploader = new Uploader($this, 'uploader');
		$uploader->cid = $this->cid;
		$uploader->lid = $lid;
	}

	/**
	 * Edit lesson page
	 * @param int $lid Lesson ID
	 */
	public function renderEdit($lid) {
		$this->checkTeacherAuthority();
		$lesson = CourseModel::getLessonByID($lid);
		$this->getComponent('editForm')->setDefaults($lesson);
	}

	/*
	 * =============================================================
	 * ==================  Signal Handlers =========================
	 */

	/**
	 * Delete lesson handler
	 * @param type $lid
	 */
	public function handleDelete($lid) {
		$this->checkTeacherAuthority();
		if (CourseModel::deleteLesson($lid)) {
			$this->flashMessage('Lesson was successfully deleted', 'success');
			$this->redirect('Course:homepage', $this->cid);
		} else
			$this->flashMessage('There was an error deleting the lesson', 'error');
	}

	/**
	 * Add comment to a lesson Form
	 * @return AppForm
	 */
	protected function createComponentCommentForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addTextArea('content', 'Comment:')
				->addRule(Form::FILLED, 'Fill comment.');
		$form->addSubmit('send', 'Post');
		$form->onSubmit[] = callback($this, 'commentFormSubmitted');

		return $form;
	}

	/**
	 * Add comment form handler
	 * @param AppForm $form
	 */
	public function commentFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (CourseModel::addComment($values, $this->lid)) {
			$this->flashMessage('Comment added.', $type = 'success');
			$this->redirect('lesson:homepage', $this->lid);
		}
		else
			$this->flashMessage('There was an error adding the comment.', $type = 'error');
	}

	/**
	 * Edit Lesson Form
	 * @return AppForm
	 */
	public function createComponentEditForm() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('topic', 'Topic:*')
				->addRule(Form::FILLED, 'Set lesson topic.');
		$form->addTextArea('description', 'Lesson description:')->getControlPrototype()->class = "texyla";
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
		CourseModel::editLesson($this->lid, $values);
		$this->flashMessage('Lesson edited', $type = 'success');
		$this->redirect('homepage');
	}

}