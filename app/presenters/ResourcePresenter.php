<?php

/**
 * Resource presenter.
 *
 */
class ResourcePresenter extends BasePresenter {

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
    
    public function actionDownload($rid){	
        // check if resource id corresponds to course id
        if (ResourceModel::getCourseIDByResourceID($rid) != $this->cid)
            throw new BadRequestException;
	$file = ResourceModel::getResource($rid);
	$this->sendResponse(new DownloadResponse(WWW_DIR.'/../uploads/'.$file->filename,$file->name));
    }
    

   
}
