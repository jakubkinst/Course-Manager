<?php

class TemplateParametersResponse extends Object implements IPresenterResponse {

    private $parameters = array();
	private $presenter;

    public function __construct(PresenterComponent $component) {
        $this->addComponent($component);
        $this->parameters = $component->template->getParams();
		unset($this->parameters['presenter']);
		unset($this->parameters['control']);
		$this->presenter = $component;
        foreach ($component->getComponents(TRUE, 'PresenterComponent') as $child) {
            $this->addComponent($child);
        }
    }

    /**
     * @param PresenterComponent $component
     */
    public function addComponent(PresenterComponent $component) {
        if (!$component->getReflection()->hasMethod('getTemplate')) {
            return;
        }

        $template = $component->getTemplate();
        if (!$template instanceof Template) {
            return;
        }

        $params = $template->getParams();
		unset($params['presenter']);
        $this->parameters[$component->getUniqueId() ? : 'presenter'] = $params;
    }

    /**
     * Sends response to output.
     * @return void
     */
    public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse) {
        $httpResponse->setHeader('Content-Type', 'application/json');
		$finalVariables = $this->presenter->processAndroidVariables($this->parameters);
        echo json_encode($finalVariables);
    }

}