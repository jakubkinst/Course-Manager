<?php

/**
 * CourseListPresenter
 *
 * @author Jakub Kinst
 */
class CourseListPresenter extends MasterPresenter {

    
    public function beforeRender() {
        $this->canbesignedout = true;
        parent::beforeRender();
    }
    
    public function actionHomepage() {
        
    }

    public function renderHomepage() {
        
    }

}