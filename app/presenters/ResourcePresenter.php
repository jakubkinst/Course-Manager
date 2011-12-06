<?php

/**
 * Resource presenter.
 *
 */
class ResourcePresenter extends BaseCoursePresenter {
    
    public $rid;
    
    protected function startup() {
	if (null != $this->getParam('rid')){	    
	    $this->rid = $this->getParam('rid');
	    $this->cid = ResourceModel::getCourseIDByResourceID($this->rid);
	}
	parent::startup();
    }

    public function actionHomepage($cid){	
        $uploader  = new Uploader($this, 'uploader');
	$uploader->cid = $cid;
    }
    /**
     * Homepage template render
     * @param type $cid 
     */
    public function renderHomepage($cid) {
        $this->template->resources = ResourceModel::getFiles($cid);
    }
    
    public function handleDelete($rid){	
	$this->checkTeacherAuthority();
	ResourceModel::deleteResource($rid);
	$this->redirect($this);
    }
    
    public function actionDownload($rid){	
	$file = ResourceModel::getResource($rid);
	$this->sendResponse(new DownloadResponse(WWW_DIR.'/../uploads/'.$file->filename,$file->name));
    }
    

   
}
