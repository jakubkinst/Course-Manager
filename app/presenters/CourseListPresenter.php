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
    
    public function renderHomepage() {      
	if ($this->logged)
		$this->template->invites = CourseListModel::getMyInvites();
    }
    
    public function handleAcceptInvite($iid){
	CourseListModel::acceptInvite($iid);
    }
    public function handleDeclineInvite($iid){
	CourseListModel::declineInvite($iid);
    }
}