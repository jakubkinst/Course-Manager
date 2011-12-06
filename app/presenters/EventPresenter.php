<?php

/**
 * Event presenter.
 *
 */
class EventPresenter extends BaseCoursePresenter {

    public $eid;
    
    protected function beforeRender() {
	parent::beforeRender();
	if (null!= $this->getParam('eid')){
	    $this->eid = $this->getParam('eid');
	    if (EventModel::getCourseIDByEventID($this->eid) != $this->cid)
		throw new BadRequestException;
	}
    }

    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
	$this->template->events = EventModel::getEvents($this->cid);
    }

    public function renderShowEvent($eid) {	
	$this->eid = $eid;
	$this->template->event = EventModel::getEvent($eid);
    }

    public function renderEdit($eid) {
	$this->checkTeacherAuthority();
	$event = EventModel::getEvent($eid);
	$event['date'] = date('m/d/Y', strtotime($event['date']));
	$this['editEvent']->setDefaults($event);
    }

    /**
     * Form factory - Add event Form
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

    public function addEventFormSubmitted($form) {
	$values = $form->getValues();
	if (EventModel::addEvent($values, $this->cid)) {
	    $this->flashMessage('Event added.', $type = 'success');
	}
	else
	    $this->flashMessage('There was an error adding the Event.', $type = 'error');
    }

    public function handleDelete($eid) {
	$this->checkTeacherAuthority();
	if (EventModel::deleteEvent($eid)) {
	    $this->flashMessage(_('Event Deleted'), 'success');
	    $this->redirect('event:homepage');
	}
	else
	    $this->flashMessage(_('There was an error deleting the event'), 'error');
    }

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

    public function editEventFormSubmitted($form) {
	$values = $form->getValues();
	if (EventModel::editEvent($this->eid, $values)) {
	    $this->flashMessage('Event edited.', $type = 'success');
	    $this->redirect('homepage');
	}
	else
	    $this->flashMessage('There was an error editting the Event.', $type = 'error');
    }

}
