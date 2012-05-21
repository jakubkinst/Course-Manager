<?php

/**
 * Presenter dedicated for debugging purposes
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
		$resp['flashmessages'] = $req->getCookies();
		$resp['users'] = dibi::fetchAll('SELECT * FROM user');
		if ($req->getQuery('mobile'))
			$this->sendResponse(new JsonResponse($resp));
	}

}
