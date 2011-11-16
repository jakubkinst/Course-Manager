<?php

/**
 * CourseListPresenter
 *
 * @author Jakub Kinst
 */
class CourseListPresenter extends MasterPresenter {

    
   public function startup() {
        $this->canbesignedout = true;
        parent::startup();
    }
    
    public function actionHomepage() {      
	//CommonModel::getTextExtract();

    }

    public function renderHomepage() {
        
    }

}