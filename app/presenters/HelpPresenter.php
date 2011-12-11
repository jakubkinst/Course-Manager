<?php

/**
 * Presenter dedicated show user manual
 * This presenter shows mainly static webpage
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class HelpPresenter extends BasePresenter {
    /*
     * =============================================================
     * =================   Parent overrides   ======================
     */

    public function startup() {
	$this->canbesignedout = true;
	parent::startup();
    }

}