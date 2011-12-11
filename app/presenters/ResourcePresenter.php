<?php

/**
 * Presenter dedicated to Resource module. Offers basic actions
 * and signals for resource download, upload, list
 * 
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters
 */
class ResourcePresenter extends BaseCoursePresenter {
    /*
     * =============================================================
     * ==================     Variables    =========================
     */

    /**
     * @var int Resource ID
     */
    public $rid;

    /*
     * =============================================================
     * =================   Parent overrides   ======================
     */

    protected function startup() {
	if (null != $this->getParam('rid')) {
	    $this->rid = $this->getParam('rid');
	    $this->cid = ResourceModel::getCourseIDByResourceID($this->rid);
	}
	parent::startup();
    }

    /*
     * =============================================================
     * =======================  Actions ============================
     */

    /**
     * Resource list
     * @param int $cid Course ID
     */
    public function actionHomepage($cid) {
	$uploader = new Uploader($this, 'uploader');
	$uploader->cid = $cid;
    }

    /**
     * Resource list
     * @param int $cid Course ID
     */
    public function renderHomepage($cid) {
	$this->template->resources = ResourceModel::getResources($cid);
    }

    /**
     * Download resource
     * @param int $rid Resource ID
     */
    public function actionDownload($rid) {
	$file = ResourceModel::getResource($rid);
	$this->sendResponse(new DownloadResponse(WWW_DIR . '/../uploads/' . $file->filename, $file->name));
    }

    /*
     * =============================================================
     * ==================  Signal Handlers =========================
     */

    /**
     * Delete resource handler
     * @param int $rid Resource ID
     */
    public function handleDelete($rid) {
	$this->checkTeacherAuthority();
	ResourceModel::deleteResource($rid);
	$this->redirect($this);
    }

}
