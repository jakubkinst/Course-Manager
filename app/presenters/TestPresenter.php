<?php

/**
 * Presenter dedicated for testing purposes
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters/Tools
 */
class TestPresenter extends Presenter {

	public function renderHomepage() {
		$req = Environment::getHttpRequest();
		$resp = array();
		$resp['error'] = "Test Error";
		$resp['dump'] = "Test Dump";
		$resp['flashmessages'] = array("Flash 1","Flash 2", "Flash 3");
		$resp['users'] = dibi::fetchAll('SELECT * FROM user');
		if ($req->getPost('mobile'))
			$this->sendResponse(new JsonResponse($resp));
	}

}
