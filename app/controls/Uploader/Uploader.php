<?php

class Uploader extends Control {

    var $cid;
    /** @persistent */
    var $f = 1;
    var $lid = null;


    public function render() {
	$this->template->f = $this->f;
	$this->template->setFile(dirname(__FILE__) . '/template.latte');
	$this->template->render();
    }

    public function handleSet($f) {
	if (!isset($f))
	    $f = 1;
	$this->f = $f;
	if ($this->presenter->isAjax())
	    $this->invalidateControl('form');
    }

    /**
     * Form factory - Upload resource Form
     * @return AppForm 
     */
    protected function createComponentUploadResource() {
	$form = new AppForm;
	for ($i = 0; $i < $this->f; $i++) {
	    $form->addFile($i, 'File '.($i+1))
		    ->addRule(Form::FILLED, 'Select file.');
	}
	$form->addSubmit('upload', 'Upload');
	$form->onSubmit[] = callback($this, 'uploadResourceFormSubmitted');
	return $form;
    }

    /**
     * Upload resource form handler
     * @param type $form 
     */
    public function uploadResourceFormSubmitted($form) {
	$values = $form->getValues();
	$values['Course_id'] = $this->cid;
	if (UploaderModel::uploadFiles($values,$this->lid)){
	    $this->flashMessage('Files successfully uploaded.', $type = 'success');
	}
	else 
	    $this->flashMessage('There was an error uploading resources.', $type = 'error');
    }

}