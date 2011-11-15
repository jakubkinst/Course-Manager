<?php

/**
 * Description of HelpPresenter
 *
 * @author JerRy
 */
class HelpPresenter extends MasterPresenter {

    public function startup() {
        $this->canbesignedout = true;
        parent::startup();
    }
    

}