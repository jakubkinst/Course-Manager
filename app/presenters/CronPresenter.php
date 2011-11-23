<?php

/**
 * Description of CronPresenter
 *
 * @author JerRy
 */
class CronPresenter extends Presenter {

    /**
     * (non-phpDoc)
     *
     * @see Nette\Application\Presenter#startup()
     */
    protected function startup() {
	parent::startup();
    }
    public function actionSendEmailsEW5q3n825mL6BM2bTZ81(){
	MailModel::sendMailsNow();
	$this->terminate();
    }

}