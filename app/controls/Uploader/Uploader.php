<?php

/**
 * Nette Control used for uploading files. Allows user to upload multiple files
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Controls
 */
class Uploader extends Control {

	/**
	 * @var int Course ID
	 */
	var $cid;

	/** @persistent
	 * @var number of file inputs
	 */
	var $f = 1;

	/**
	 * @var int Lesson ID if attaching files to a lesson
	 */
	var $lid = null;

	/**
	 * Renders control
	 */
	public function render() {
		$this->template->f = $this->f;
		$this->template->setFile(dirname(__FILE__) . '/template.latte');
		$this->template->render();
	}

	/**
	 * Sets amount of file inputs
	 * @param int $f
	 */
	public function handleSet($f) {
		if (!isset($f))
			$f = 1;
		$this->f = $f;
		if ($this->presenter->isAjax())
			$this->invalidateControl('form');
	}

	/**
	 * Upload resource Form
	 * @return AppForm
	 */
	protected function createComponentUploadResource() {
		$form = new AppForm;
		for ($i = 0; $i < $this->f; $i++) {
			$form->addFile($i, 'File ' . ($i + 1))
					->addRule(Form::FILLED, 'Select file.');
		}
		$form->addSubmit('upload', 'Upload');
		$form->onSubmit[] = callback($this, 'uploadResourceFormSubmitted');
		return $form;
	}

	/**
	 * Upload resource form handler
	 * @param AppForm $form
	 */
	public function uploadResourceFormSubmitted(AppForm $form) {
		$values = $form->getValues();
		$values['Course_id'] = $this->cid;
		if (UploaderModel::uploadFiles($values, $this->lid)) {
			$this->presenter->flashMessage('Files successfully uploaded.', $type = 'success');
		}
		else
			$this->presenter->flashMessage('There was an error uploading resources.', $type = 'error');
	}

}