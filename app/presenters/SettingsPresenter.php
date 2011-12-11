<?php

/**
 * Presenter dedicated to User settings
 * 
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class SettingsPresenter extends BasePresenter {
    /*
     * =============================================================
     * =======================  Actions ============================
     */

    /**
     * Settings homepage
     * @param type $cid 
     */
    public function actionHomepage($email) {

	// set default values
	$form = $this->getComponent('settings');
	$settings = SettingsModel::getMySettings();
	unset($settings->User_id);
	$form->setDefaults($settings);
    }

    /*
     * =============================================================
     * ==================  Form factories  =========================
     */

    /**
     * Add topic Form
     * @return AppForm 
     */
    protected function createComponentSettings() {

	$form = new AppForm;
	$form->setTranslator($this->translator);
	$form->addCheckbox('showEmail', 'Show my e-mail address to my coursemates');
	$langs = array('cs' => _('Czech'), 'en' => _('English'));
	$form->addSelect('lang', 'Preffered language', $langs);
	$days = array(1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10');
	$form->addSelect('assignment_notif_interval', 'Notify X days before assignment duedate', $days);
	$form->addSubmit('post', 'Save');
	$form->onSubmit[] = callback($this, 'SettingsFormSubmitted');
	return $form;
    }

    /**
     * Add Topic form handler
     * @param AppForm $form 
     */
    public function SettingsFormSubmitted(AppForm $form) {
	$values = $form->getValues();
	if (SettingsModel::setSettings($values)) {
	    $this->flashMessage('Settings saved.', $type = 'success');
	}
	else
	    $this->flashMessage('There was an error saving the settings.', $type = 'error');
    }

}
