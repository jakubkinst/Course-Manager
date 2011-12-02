<?php

/**
 * Event presenter.
 *
 */
class EventPresenter extends BaseCoursePresenter {
    
    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->checkAuthorization();        
        $this->template->events = EventModel::getEvents($this->cid);        
    }
    
    public function renderShowEvent($eid){
        // check if event id corresponds to course id
        if (EventModel::getCourseIDByEventID($eid) != $this->cid) 
            throw new BadRequestException;
        $this->checkTeacherAuthority();      
        $this->template->event = EventModel::getEvent($eid);
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
                ->addRule(Form::FILLED, 'Fill date.');
        $form->addTextArea('description', 'Description:*')
                ->addRule(Form::FILLED, 'Fill description.');

        $form->addSubmit('post', 'Add');
        $form->onSubmit[] = callback($this, 'addEventFormSubmitted');
        return $form;
    }

    /**
     * add topic form handler
     * @param type $form 
     */
    public function addEventFormSubmitted($form) {
        $values = $form->getValues();
        if (EventModel::addEvent($values,$this->cid)){        
            $this->flashMessage('Event added.', $type = 'success');
        }
        else 
            $this->flashMessage('There was an error adding the Event.', $type = 'error');
        
    }   
}
