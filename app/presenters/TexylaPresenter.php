<?php

/**
 * Presenter used to display texyla preview
 * 
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters/Tools
 */
class TexylaPresenter extends Presenter {

    public function actionPreview($texy) {
	$texyInstance = new Texy;
	echo $texyInstance->process($texy);
	$this->terminate();
    }

}

?>