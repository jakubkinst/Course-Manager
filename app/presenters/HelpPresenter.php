<?php

/**
 * Description of HelpPresenter
 *
 * @author JerRy
 */
class HelpPresenter extends BasePresenter {

    public function startup() {
        $this->canbesignedout = true;
        parent::startup();
    }
    

}