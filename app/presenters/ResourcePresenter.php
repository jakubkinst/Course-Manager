<?php

/**
 * Resource presenter.
 *
 */
class ResourcePresenter extends BasePresenter {


    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->checkAuthorization();
        $this->template->resources = ResourceModel::getFiles($cid);
        
    }
    
     /**
     * Form factory - Upload resource Form
     * @return AppForm 
     */
    protected function createComponentUploadResource() {
        $form = new AppForm;
        $form->addFile('file', 'File:*')
                ->addRule(Form::FILLED, 'Select file.');
        $form->addText('name', 'Name:*')        
                ->addRule(Form::FILLED, 'Fill filename.');
        $form->addText('description', 'Description:');

        $form->addSubmit('upload', 'Upload');
        $form->onSubmit[] = callback($this, 'uploadResourceFormSubmitted');
        $form->addHidden('Course_id', $this->cid);
        return $form;
    }

    /**
     * Upload resource form handler
     * @param type $form 
     */
    public function uploadResourceFormSubmitted($form) {
        $values = $form->getValues();
        $values['added'] = new DateTime;
        if (ResourceModel::uploadFile($values))
            $this->flashMessage('File successfully uploaded', $type = 'success');
        else
            $this->flashMessage('File upload error', $type = 'error');
    }

   
}
