<?php

class TemplateParametersResponse extends Nette\Object implements \Nette\Application\IResponse {

    private $parameters = array();
	private $presenter;

    public function __construct(Nette\Application\UI\PresenterComponent $component) {
        $this->addComponent($component);
        $this->parameters = $component->template->getParameters();
		unset($this->parameters['presenter']);
		unset($this->parameters['control']);
		$this->presenter = $component;
        foreach ($component->getComponents(TRUE, 'Nette\Application\UI\PresenterComponent') as $child) {
            $this->addComponent($child);
        }
    }

    /**
     * @param PresenterComponent $component
     */
    public function addComponent(Nette\Application\UI\PresenterComponent $component) {
        if (!$component->getReflection()->hasMethod('getTemplate')) {
            return;
        }

        $template = $component->getTemplate();
        if (!$template instanceof Template) {
            return;
        }

        $params = $template->getParameters();
		unset($params['presenter']);
        $this->parameters[$component->getUniqueId() ? : 'presenter'] = $params;
    }

    /**
     * Sends response to output.
     * @return void
     */
    public function send(Nette\Http\IRequest $httpRequest, Nette\Http\IResponse $httpResponse) {
        $httpResponse->setHeader('Content-Type', 'application/json');
		$finalVariables = $this->presenter->processAndroidVariables($this->parameters);
        echo json_encode($finalVariables);
    }

}