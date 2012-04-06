<?php

/**
 * Presenter dedicated to be used by cron or other task scheduler.
 * It provides methods that are supposed to run once ine a gevin period
 * of time. Hashes behind the name of presenter is for security reasons.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Presenters/Tools
 */
class CronPresenter extends Presenter {

	/**
	 * Send All emails which are prepared for sending
	 */
	public function actionSendEmailsEW5q3n825mL6BM2bTZ81() {
		MailModel::sendMailsNow();
		$this->terminate();
	}

	/**
	 * Delete users who havent confirmed email
	 */
	public function actionDeleteUncheckedUsersEW5q3n825mL6BM2bTZ81() {
		UserModel::deleteUncheckedUsers();
		$this->terminate();
	}

	/**
	 * Send Assignment notifications
	 */
	public function actionSendAssignmentNotificationsEW5q3n825mL6BM2bTZ81() {
		$link = $this->getHttpRequest()->getUri()->getBaseUri();
		AssignmentModel::sendAssignmentNotifications($link);
		$this->terminate();
	}

	/**
	 * Clean webtemp directory from old files (older than 30 minutes)
	 */
	public function actionDeleteOldTempFilesEW5q3n825mL6BM2bTZ81() {

		$dirName = WWW_DIR . '/webtemp'; // Path to the cache directory
		$timeLimit = 30; // Max time since a file has been accessed
		//   before it should be deleted

		if (is_dir($dirName)) {
			$files = scandir($dirName);
		}

		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}

			if (fileatime($dirName . '/' . $file) < time() - ($timeLimit * 60)) {
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