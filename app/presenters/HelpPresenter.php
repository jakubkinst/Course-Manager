<?php

/**
 * Description of HelpPresenter
 *
 * @author JerRy
 */
class HelpPresenter extends MasterPresenter {

    public function beforeRender() {
        $this->canbesignedout = true;
        parent::beforeRender();
    }
    

}