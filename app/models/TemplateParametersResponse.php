<?php

class TemplateParametersResponse extends Object implements IPresenterResponse {

    private $parameters = array();

    public function __construct(PresenterComponent $component) {
        $this->addComponent($component);
        $this->parameters = $component->template->getParams();
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
        $this->parameters[$component->getUniqueId() ? : 'presenter'] = $params;
    }

    /**
     * Sends response to output.
     * @return void
     */
    public function send(IHttpRequest $httpRequest, IHttpResponse $httpResponse) {
        $httpResponse->setHeader('Content-Type', 'application/json');
        echo json_encode($this->parameters);
    }

}