<?php

/**
 * Presenter dedicated to show course events and handle related signals and actions
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class EventPresenter extends BaseCoursePresenter {
	/*
	 * =============================================================
	 * ==================     Variables    =========================
	 */

	/**
	 * @var int Event ID
	 */
	public $eid;

	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	protected function startup() {
		if (null != $this->getParam('eid')) {
			$this->eid = $this->getParam('eid');
			$this->cid = EventModel::getCourseIDByEventID($this->eid);
		}
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Homepage template render
	 * @param int $cid Course ID
	 */
	public function renderHomepage($cid) {
		$this->template->events = EventModel::getEvents($this->cid);
	}

	/**
	 * Show event detail
	 * @param int $eid Event ID
	 */
	public function renderShowEvent($eid) {
		$this->template->event = EventModel::getEvent($eid);
	}

	/**
	 * Edit Event page
	 * @param int $eid Event ID
	 */
	public function renderEdit($eid) {
		$this->checkTeacherAuthority();
		$event = EventModel::getEvent($eid);
		$event['date'] = date('m/d/Y', strtotime($event['date']));
		$this['editEvent']->setDefaults($event);
	}

	/*
	 * =============================================================
	 * ==================  Signal Handlers =========================
	 */

	public function handleDelete($eid) {
		$this->checkTeacherAuthority();
		if (EventModel::deleteEvent($eid)) {
			$this->flashMessage(_('Event Deleted'), 'success');
			$this->redirect('event:homepage', $this->cid);
		}
		else
			$this->flashMessage(_('There was an error deleting the event'), 'error');
	}

	/*
	 * =============================================================
	 * ==================  Form factories  =========================
	 */

	/**
	 * Add event Form
	 * @return AppForm
	 */
	protected function createComponentAddEvent() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('name', 'Name:*')
				->addRule(Form::FILLED, 'Fill name.');
		$form->addText('date', 'Date:*')
						->addRule(Form::FILLED, 'Fill date.')
						->getControlPrototype()->class = "datepicker";
		$form->addTextArea('description', 'Description:*')
				->addRule(Form::FILLED, 'Fill description.');

		$form->addSubmit('post', 'Add');
		$form->onSubmit[] = callback($this, 'addEventFormSubmitted');
		return $form;
	}

	/**
	 * Add Event form handler
	 * @param AppForm $form
	 */
	public function addEventFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (EventModel::addEvent($values, $this->cid)) {
			$this->flashMessage('Event added.', $type = 'success');
			$this->redirect('this');
		}
		else
			$this->flashMessage('There was an error adding the Event.', $type = 'error');
	}

	/**
	 * Edit Event form
	 * @return AppForm
	 */
	protected function createComponentEditEvent() {
		$form = new AppForm;
		$form->setTranslator($this->translator);
		$form->addText('name', 'Name:*')
				->addRule(Form::FILLED, 'Fill name.');
		$form->addText('date', 'Date:*')
						->addRule(Form::FILLED, 'Fill date.')
						->getControlPrototype()->class = "datepicker";
		$form->addTextArea('description', 'Description:*')
				->addRule(Form::FILLED, 'Fill description.');

		$form->addSubmit('post', 'Save');
		$form->onSubmit[] = callback($this, 'editEventFormSubmitted');
		return $form;
	}

	/**
	 * Edit event form handler
	 * @param AppForm $form
	 */
	public function editEventFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		if (EventModel::editEvent($this->eid, $values)) {
			$this->flashMessage('Event edited.', $type = 'success');
			$this->redirect('homepage');
		}
		else
			$this->flashMessage('There was an error editting the Event.', $type = 'error');
	}

}
