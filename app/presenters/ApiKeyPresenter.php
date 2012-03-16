<?php

/**
 * Description of ApiKeyPresenter
 *
 * @author Jakub Kinst
 */
class ApiKeyPresenter extends BasePresenter {

	/**
	 * (non-phpDoc)
	 *
	 * @see Nette\Application\Presenter#startup()
	 */
	protected function startup() {
		parent::startup();
	}

	public function actionHomepage() {
		$this->template->myApiKey = UserModel::getLoggedUser()->apiKey;
	}

	protected function createComponentApiKeyGenerator() {

		$form = new AppForm;
		$form->addSubmit('post', 'Regenerate');
		$form->onSubmit[] = callback($this, 'regenerateApiKey');
		return $form;
	}

	function regenerateApiKey($form) {
		if (UserModel::regenerateApiKey($this->getUser()->id)) {
			$this->flashMessage(_('User api key regenerated successfully'), 'success');
			$this->redirect('this');
		}
		else
			$this->flashMessage(_('User api key could not be regenerated'), 'error');
	}

}