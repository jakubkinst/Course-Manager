<?php

/**
 * Presenter dedicated to mini-forum included in the application
 * handles related signals and actions
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class ForumPresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * ==================     Variables    =========================
	 */

	/**
	 * @var int Topic ID
	 */
	public $tid;

	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	protected function startup() {
		if (null != $this->getParam('tid')) {
			$this->tid = $this->getParam('tid');
			$this->cid = ForumModel::getCourseIDByTopicID($this->tid);
		}
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Homepage
	 * @param int $cid Course ID
	 */
	public function renderHomepage($cid) {
		$this->paginator->itemsPerPage = 20;
		$this->paginator->itemCount = ForumModel::countTopics($cid);
		$topics = ForumModel::getTopics($cid, $this->paginator->offset, $this->paginator->itemsPerPage);
		$this->template->topics = $topics;
	}

	/**
	 * Show topic and list replies
	 * @param int $tid Topic ID
	 */
	public function renderShowTopic($tid) {

		// paging control
		$this->paginator->itemsPerPage = 20;
		$this->paginator->itemCount = ForumModel::countReplies($tid);

		//pass variables
		$this->template->topic = ForumModel::getTopic($tid);
		$this->template->replies = ForumModel::getReplies($tid, $this->paginator->offset, $this->paginator->itemsPerPage);
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Add topic Form
	 * @return AppForm
	 */
	protected function createComponentAddTopic() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('label', 'Topic:*')
				->addRule(Form::FILLED, 'Fill topic.');
		$form->addTextArea('content', 'Content:*')
				->addRule(Form::FILLED, 'Fill content.');

		$form->addSubmit('post', 'Post');
		$form->onSubmit[] = callback($this, 'addTopicFormSubmitted');
		return $form;
	}

	/**
	 * Add topic form handler
	 * @param AppForm $form
	 */
	public function addTopicFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (ForumModel::addTopic($values, $this->cid)) {
			$this->flashMessage('Topic added.', $type = 'success');
		}
		else
			$this->flashMessage('There was an error adding the Topic.', $type = 'error');
	}

	/**
	 * Add reply Form
	 * @return AppForm
	 */
	protected function createComponentAddReply() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addTextArea('content', 'Reply:*')
				->addRule(Form::FILLED, 'Fill content.');
		$form->addSubmit('post', 'Post');
		$form->onSubmit[] = callback($this, 'addReplyFormSubmitted');
		return $form;
	}

	/**
	 * Add reply form handler
	 * @param AppForm $form
	 */
	public function addReplyFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (ForumModel::addReply($values, $this->getParam('tid'))) {
			$this->flashMessage('Reply added.', $type = 'success');
		}
		else
			$this->flashMessage('There was an error adding the Reply.', $type = 'error');
	}

}
