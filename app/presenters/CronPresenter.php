<?php

/**
 * Description of CronPresenter
 *
 * @author JerRy
 */
class CronPresenter extends Presenter {

    public function actionSendEmailsEW5q3n825mL6BM2bTZ81() {
	MailModel::sendMailsNow();
	$this->terminate();
    }

    //delete users who havent confirmed email
    public function actionDeleteUncheckedUsersEW5q3n825mL6BM2bTZ81() {
	UserModel::deleteUncheckedUsers();
	$this->terminate();
    }

    public function actionSendAssignmentNotificationsEW5q3n825mL6BM2bTZ81() {
	AssignmentModel::sendAssignmentNotifications();
	$this->terminate();
    }

    // delete 30 minutes old webtemp files
    public function actionDeleteOldTempFilesEW5q3n825mL6BM2bTZ81() {	
	
	$dirName = WWW_DIR.'/webtemp'; // Path to the cache directory
	$timeLimit = 30; // Max time since a file has been accessed
	//   before it should be deleted

	if (is_dir($dirName)) {
	    $files = scandir($dirName);
	}

	foreach ($files as $file) {
	    if ($file == '.' || $file == '..') {
		continue;
	    }

	    if (fileatime($dirName . '/' . $file) < time() - ($timeLimit *60)) {
		if (unlink($dirName . '/' . $file)) {
		    echo "$file deleted successfully.";
		} else {
		    echo "$file not deleted.";
		}
		echo '<br />';
	    }
	}	
	$this->terminate();
    }

}