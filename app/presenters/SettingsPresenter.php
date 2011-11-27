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
	$form->setTranslator($this->translator);     
        $form->addCheckbox('showEmail', 'Show my e-mail address to my coursemates');
	$langs =array('cs' => _('Czech'),'en' => _('English'));
	$form->addSelect('lang', 'Preffered language', $langs);
	$days = array(1=>'1',2=>'2',3=>'3',4=>'4',5=>'5',6=>'6',7=>'7',8=>'8',9=>'9',10=>'10');
	$form->addSelect('assignment_notif_interval', 'Notify X days before assignment duedate', $days);
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
