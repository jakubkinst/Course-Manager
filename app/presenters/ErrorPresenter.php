<?php

/**
 * Error presenter shows error messages.
 * 
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters/Tools
 */
class ErrorPresenter extends BaseCoursePresenter {

    /**
     * Error homepage
     * @param  Exception
     * @return void
     */
    public function renderDefault($exception) {
	$this->setView('500');
    }

}
