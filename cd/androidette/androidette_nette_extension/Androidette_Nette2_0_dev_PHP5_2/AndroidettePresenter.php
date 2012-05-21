<?php

/**
 * Description of AndroidettePresenter
 *
 * @author Jakub Kinst
 */
class AndroidettePresenter extends Presenter {

	/**
	 * @persistent
	 * True if accessing from Android App
	 * @var boolean
	 */
	public $mobile;

	protected function startup() {
		parent::startup();
		if ($this->mobile == null)
			$this->mobile = $this->getParam('mobile');
	}

	protected function afterRender() {
		parent::afterRender();
		Debug::barDump($this->template->getParams());
		if ($this->mobile) {
			ob_start();
			$files = $this->formatTemplateFiles($this->getName(), $this->view);
			foreach ($files as $file) {
				if (is_file($file)) {
					$this->template->setFile($file);
					break;
				}
			}
			$this->template->render();
			ob_end_clean();
			$this->sendResponse(new TemplateParametersResponse($this));
		}
	}

	public function processAndroidVariables($variables) {
		return $variables;
	}

}