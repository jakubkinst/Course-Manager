<?php

/**
 * Description of CallbackPresenter
 *
 * @author Jakub Kinst
 */
class CallbackPresenter extends Presenter {

	function actionHomepage($user,$password, $number) {
		echo $user;
		$this->terminate();
	}

}