<?php

/**
 * Settings presenter.
 *
 */
class SettingsPresenter extends MasterPresenter {   
    /**
     * Homepage template render
     * @param type $cid 
     */    
    public function actionHomepage($email){
        $form = $this->getComponent('settings');
        $settings = SettingsModel::getMySettings();
        unset($settings->User_id);
        $form->setDefaults($settings);
    }
        
     /**
     * Form factory - Add topic Form
     * @return AppForm 
     */
    protected function createComponentSettings() {
        
        $form = new AppForm;        
        $form->addCheckbox('showEmail', 'Show my e-mail address to my coursemates');        
        $form->addSubmit('post', 'Save');    
        $form->onSubmit[] = callback($this, 'SettingsFormSubmitted');
        return $form;
    }

    /**
     * add topic form handler
     * @param type $form 
     */
    public function SettingsFormSubmitted($form) {
        $values = $form->getValues();
        if (SettingsModel::setSettings($values)){        
            $this->flashMessage('Settings saved.', $type = 'success');
        }
        else 
            $this->flashMessage('There was an error saving the settings.', $type = 'error');
        
    }
       
}
