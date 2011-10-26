<?php

/**
 * CourseListPresenter
 *
 * @author Jakub Kinst
 */
class CourseListPresenter extends BasePresenter {

    
    public function beforeRender() {
        $this->canbesignedout = true;
        parent::beforeRender();
    }
    
    public function actionHomepage() {
        
    }

    public function renderHomepage() {
        
    }

}