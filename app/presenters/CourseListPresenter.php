<?php

/**
 * Presenter dedicated to display homepage
 * Homepage is list of courses if the user is logged or the welcome-page if the user is not logged
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class CourseListPresenter extends BasePresenter {
	/*
	 * =============================================================
	 * =================   Parent overrides   ======================
	 */

	public function startup() {
		$this->canbesignedout = true;
		parent::startup();
	}

	/*
	 * =============================================================
	 * =======================  Actions ============================
	 */

	/**
	 * Homepage
	 */
	public function renderHomepage() {
		if ($this->logged)
			$this->template->invites = CourseListModel::getMyInvites();
	}

	/*
	 * =============================================================
	 * ==================  Signal Handlers =========================
	 */

	/**
	 * Accepting an invite to a course
	 */
	public function handleAcceptInvite($iid) {
		CourseListModel::acceptInvite($iid);
	}

	/**
	 * Declining an invite to a course
	 */
	public function handleDeclineInvite($iid) {
		CourseListModel::declineInvite($iid);
	}

}