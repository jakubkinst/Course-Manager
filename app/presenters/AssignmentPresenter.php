<?php

/**
 * Description of AssignmentPresenter
 *
 * @author JerRy
 */
class AssignmentPresenter extends BasePresenter {
    var $aid = 0;

    public function renderHomepage($cid) {
	$this->template->assignments = AssignmentModel::getAssignments($this->cid);
    }
    
    public function renderAdd($cid){
	$this->checkTeacherAuthority();
	
    }
    
     protected function createComponentAddAssignmentForm() {
	$form = new AppForm;
	$form->addText('name', 'Name*')
		->setRequired();
	$form->addTextArea('description', 'Description:');
	$form->addText('assigndate','Open Date:');
	$form->addText('duedate', 'Close Date:');
	$form->addText('maxpoints','Max. Points (0=no max points):')
		->setDefaultValue('0')
		->addRule(Form::INTEGER, 'Max. points must be a number');	
	$form->addText('timelimit','Time limit in minutes (0=no limit):')
		->addRule(Form::INTEGER, 'Time limit must be a number')
		->setDefaultValue('0')
		->addRule(Form::INTEGER, 'Max. points must be a number');
	$form->addSubmit('submit', 'Add');
	$form->onSubmit[] = callback($this, 'addAssignment');
	return $form;
    }

    public function addAssignment($form) {
	$values = $form->getValues();
	$newaid = AssignmentModel::addNormalAssignment($values, $this->cid);
	if ($newaid != -1){        
            $this->redirect('edit',$newaid);
        }
        else 
            $this->flashMessage('There was an error adding the Assignment.', $type = 'error');
        
    }

    public function renderEdit($aid){
	$this->checkTeacherAuthority();
	// TODO: check aid and cid
	$this->template->questions = AssignmentModel::getQuestions($aid);	
	$this->template->assignment = AssignmentModel::getAssignment($aid);
	if ($this->isAjax()) {
	    $this->invalidateControl('virtualFormSnippet');
	    $this->invalidateControl('virtualFormSnippet');
	}
    }

    public function actionEdit($aid) {		
	// check if assignment id corresponds to course id
        if (AssignmentModel::getCourseIDByAssignmentID($aid) != $this->cid) {
            throw new BadRequestException;
        }
	$this->aid = $aid;
    }

    protected function createComponentVirtualForm() {
	$form = new AppForm;
	foreach ($this->template->questions as $value) {
	    $label = Html::el()->setHtml(htmlspecialchars($value->label) .
			    '<a class="ajax" href="' . $this->link('remove!', $value->id) . '"><span class="ui-icon ui-icon-trash"></span></a>');
	    if ($value->type == 'text')
		$form->addText('input' . $value->id, $label);
	    if ($value->type == 'textarea')
		$form->addTextArea('input' . $value->id, $label);
	    if ($value->type == 'radio')
		$form->addRadioList('input' . $value->id, $label, AssignmentModel::parseChoices($value->choices));
	    if ($value->type == 'checkbox')
		$form->addCheckbox('input' . $value->id, $label);
	    if ($value->type == 'multi')
		$form->addMultiSelect('input' . $value->id, $label, AssignmentModel::parseChoices($value->choices));
	}

	return $form;
    }

    public function handleRemove($qid) { 
	// check if question id corresponds to course id
        if (AssignmentModel::getCourseIDByQuestionID($qid) != $this->cid) {
            throw new BadRequestException;
        }
	AssignmentModel::removeQuestion($qid);
    }

    protected function createComponentAddTextForm() {
	$form = new AppForm;
	$form->addGroup('Example');
	$form->addText('example', "My Label")->setDisabled();	
	$form->addGroup('Set Label');
	$form->addTextArea('label');
	$form->addSubmit('add', 'Add');
	$form->onSubmit[] = callback($this, 'addText');
	return $form;
    }

    public function addText($form) {
	$values = $form->getValues();
	AssignmentModel::addText($values['label'], $this->aid);
    }

    protected function createComponentAddTextAreaForm() {
	$form = new AppForm;
	$form->addGroup('Example');
	$form->addTextArea('example', "My Label")->setDisabled();	
	$form->addGroup('Set Label');
	$form->addTextArea('label');
	$form->addSubmit('add', 'Add');
	$form->onSubmit[] = callback($this, 'addTextArea');
	return $form;
    }

    public function addTextArea($form) {
	$values = $form->getValues();
	AssignmentModel::addTextArea($values['label'], $this->aid);
    }

    protected function createComponentAddRadioForm() {
	$form = new AppForm;
	$form->addGroup('Example');
	$form->addRadioList('example', "My Label",array('option 1','option 2','option 3'))->setDisabled();	
	$form->addGroup('Set Label and choices');
	$form->addTextArea('label');
	$form->addText('val1');
	$form->addText('val2');
	$form->addText('val3');
	$form->addText('val4');
	$form->addText('val5');
	$form->addText('val6');
	$form->addText('val7');
	$form->addText('val8');
	$form->addSubmit('add', 'Add');
	$form->onSubmit[] = callback($this, 'addRadio');
	return $form;
    }

    public function addRadio($form) {
	$values = $form->getValues();
	$choices = array(
	    $values['val1'],
	    $values['val2'],
	    $values['val3'],
	    $values['val4'],
	    $values['val5'],
	    $values['val6'],
	    $values['val7'],
	    $values['val8']
	);
	AssignmentModel::addRadio($values['label'], $choices, $this->aid);
    }

    protected function createComponentAddCheckboxForm() {
	$form = new AppForm;
	$form->addGroup('Example');
	$form->addCheckbox('example', "My Label")->setDisabled()->setValue(true);	
	$form->addGroup('Set Label');
	$form->addTextArea('label');
	$form->addSubmit('add', 'Add');
	$form->onSubmit[] = callback($this, 'addCheckbox');
	return $form;
    }

    public function addCheckbox($form) {
	$values = $form->getValues();
	AssignmentModel::addCheckbox($values['label'], $this->aid);
    }

    protected function createComponentAddMultiSelectForm() {
	$form = new AppForm;
	$form->addGroup('Example');
	$form->addMultiSelect('example', "My Label",array('option 1','option 2','option 3'))->setDisabled()->setDefaultValue(array(1,2));	
	$form->addGroup('Set Label and choices');
	$form->addTextArea('label');
	$form->addText('val1');
	$form->addText('val2');
	$form->addText('val3');
	$form->addText('val4');
	$form->addText('val5');
	$form->addText('val6');
	$form->addText('val7');
	$form->addText('val8');
	$form->addSubmit('add', 'Add');
	$form->onSubmit[] = callback($this, 'addMultiSelect');
	return $form;
    }

    public function addMultiSelect($form) {
	$values = $form->getValues();
	$choices = array(
	    $values['val1'],
	    $values['val2'],
	    $values['val3'],
	    $values['val4'],
	    $values['val5'],
	    $values['val6'],
	    $values['val7'],
	    $values['val8']
	);
	AssignmentModel::addMultiSelect($values['label'], $choices, $this->aid);
    }

}