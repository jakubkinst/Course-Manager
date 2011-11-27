<?php

/**
 * AssignmentModel
 *
 * @author Jakub Kinst
 */
class AssignmentModel extends Object {

    public static function addAssignment($values, $cid) {
	$values['Course_id'] = $cid;
	$values['created'] = new DateTime;
	$values['assigndate'] = CommonModel::convertFormDate($values['assigndate']);
	$values['duedate'] = CommonModel::convertFormDate($values['duedate']);
	if (dibi::query('INSERT INTO assignment', $values)) {
	    $id = dibi::getInsertId();
	    self::sendNewAssignmentNotif($id);
	    return $id;
	}
	else
	    return -1;
    }

    public static function addText($label, $aid, $ra) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'text', 'rightanwser' => $ra, 'label' => $label));
    }

    public static function addTextArea($label, $aid) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'textarea', 'label' => $label));
    }

    public static function addFile($label, $aid) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'file', 'label' => $label));
    }

    public static function addRadio($label, $choices, $aid, $ra) {

	function isEmpty($var) {
	    return(!($var == ''));
	}

	$choices = array_filter($choices, 'isEmpty');
	$choices2 = implode('#', $choices);
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'radio', 'rightanwser' => $ra, 'label' => $label, 'choices' => $choices2));
    }

    public static function addMultiSelect($label, $choices, $aid, $ra) {

	function isEmpty($var) {
	    return(!($var == ''));
	}

	$choices = array_filter($choices, 'isEmpty');
	$choices2 = implode('#', $choices);
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'multi', 'rightanwser' => $ra, 'label' => $label, 'choices' => $choices2));
    }

    public static function getUserSubmission($aid, $uid) {
	$submission = dibi::fetchPairs(
			'SELECT Question_id,anwser.anwser FROM anwser 
		    JOIN question ON Question_id = question.id
		    JOIN assignment ON Assignment_id = assignment.id
			WHERE assignment.id=%i AND User_id=%i
			ORDER BY question.id ASC'
			, $aid, $uid);

	return $submission;
    }

    public static function saveResult($aid, $values) {
	foreach ($values as $uid => $pts)
	    dibi::query('UPDATE onlinesubmission SET points=%i', $pts, 'WHERE User_id=%i AND Assignment_id=%i', $uid, $aid);
	return true;
    }

    public static function getStudentsWithSubmissionWithoutResult($aid) {
	return dibi::fetchAll('SELECT User_id FROM onlinesubmission WHERE Assignment_id=%i AND points IS NULL', $aid);
    }

    public static function getSubmissions($aid) {
	$students = self::getStudentsWithSubmissionWithoutResult($aid);
	$submissions = array();
	foreach ($students as $student) {
	    $userSubmission = self::getUserSubmission($aid, $student);
	    $userSubmission['user'] = UserModel::getUser($student);
	    array_push($submissions, $userSubmission);
	}
	return $submissions;
    }

    public static function getQuestions($aid) {
	$q = dibi::fetchAll('SELECT * FROM question WHERE Assignment_id=%i ORDER BY id ASC', $aid);
	return $q;
    }

    public static function getQuestion($qid) {
	$q = dibi::fetch('SELECT * FROM question WHERE id=%i', $qid);
	return $q;
    }

    public static function parseChoices($str) {
	return explode('#', $str);
    }

    public static function removeQuestion($qid) {
	return dibi::query('DELETE FROM question WHERE id=%i', $qid) &&
	dibi::query('DELETE FROM anwser WHERE Question_id=%i', $qid);
    }

    public static function getCourseIDByAssignmentID($aid) {
	return dibi::fetchSingle('SELECT Course_id FROM assignment WHERE id=%i', $aid);
    }

    public static function getCourseIDByQuestionID($qid) {
	return dibi::fetchSingle('SELECT Course_id FROM question JOIN assignment ON Assignment_id=assignment.id WHERE question.id=%i', $qid);
    }

    public static function getAssignment($aid) {
	$q = dibi::fetch('SELECT * FROM assignment WHERE id=%i', $aid);
	return $q;
    }

    public static function getAssignments($cid) {
	$q = dibi::fetchAll('SELECT * FROM assignment WHERE Course_id=%i ORDER BY duedate ASC', $cid);
	return $q;
    }

    public static function startSolving($aid) {
	$values = array(
	    'Assignment_id' => $aid,
	    'started' => new DateTime,
	    'User_id' => UserModel::getUserID(Environment::getUser()->getIdentity())
	);
	return dibi::query('INSERT INTO onlinesubmission', $values);
    }

    public static function getCorrected($values, $aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$cor = 0;
	$i = 0;
	foreach ($values as $key => $value) {
	    $i++;
	    $q = self::getQuestion($key);
	    $stranwser = array();
	    $choices = explode('#', $q->choices);
	    if ($value != null && $value != '') {
		// get real string anwsers and split anwsers for radios and multiselects
		if ($q->type == 'multi') {
		    foreach ($value as $a) {
			array_push($stranwser, $choices[$a]);
		    }
		    $value = implode('#', $value);
		    $value = implode('#', $stranwser);
		}
		if ($q->type == 'radio') {
		    $stranwser = $choices[$value];
		    $value = $stranwser;
		}
		// $key ... Question id;
		// $value ...Anwser
		if (trim($q->rightanwser) == trim($value))
		    $cor++;
	    }
	}
	$score = intval(round(self::getAssignment($aid)->maxpoints * $cor / $i, 0));
	dibi::query('UPDATE onlinesubmission SET points=%i', $score, 'WHERE User_id=%i AND Assignment_id=%i', $uid, $aid);
	return $score;
    }

    public static function submitSubmission($values, $aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$ok = true;
	foreach ($values as $key => $value) {
	    $q = self::getQuestion($key);
	    $stranwser = array();
	    $choices = explode('#', $q->choices);
	    if ($value != null && $value != '') {
		// get real string anwsers and split anwsers for radios and multiselects
		if ($q->type == 'multi') {
		    foreach ($value as $a) {
			array_push($stranwser, $choices[$a]);
		    }
		    $value = implode('#', $value);
		    $value = implode('#', $stranwser);
		}
		if ($q->type == 'radio') {
		    $stranwser = $choices[$value];
		    $value = $stranwser;
		}
		if ($q->type == 'file') {
		    dibi::begin();
		    $value = self::uploadAnwserFile($value);
		}
		$result = true;
		if (null == dibi::fetch('SELECT * FROM anwser WHERE User_id=%i AND Question_id=%i', $uid, $key))
		    $result = dibi::query('INSERT INTO anwser', array('User_id' => $uid, 'Question_id' => $key, 'anwser' => $value));
		else
		    dibi::query('UPDATE anwser SET', array('anwser' => $value), 'WHERE User_id=%i AND Question_id=%i', $uid, $key);
		if (!$result)
		    $ok = false;
	    }
	}

	return $ok;
    }

    public static function uploadAnwserFile($file) {
	$data = array();
	$data['size'] = $file->getSize();
	$hashfilename = md5($_SERVER["REMOTE_ADDR"] . time()) . "_" . $file->getName();
	if ($file->isOK()) {
	    if ($file->move(WWW_DIR . "/../" . ResourceModel::$UPLOAD_DIR . "/" . $hashfilename)) {
		$data['filename'] = $hashfilename;
		dibi::begin();
		dibi::query('INSERT INTO anwserfile', $data);
		$id = dibi::getInsertId();
		dibi::commit();
		return $id;
	    }
	}
	return false;
    }

    public static function getAnwsers($aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$anwsers = dibi::fetchAll('SELECT Question_id, anwser,type,choices FROM anwser JOIN question ON question.id=Question_id WHERE Assignment_id=%i AND User_id=%i', $aid, $uid);
	$assocanwsers = array();
	foreach ($anwsers as $a) {
	    if ($a->type == 'radio') {
		$a->anwser = array_search($a->anwser, explode('#', $a->choices));
	    }
	    if ($a->type == 'multi') {
		$intarr = array();
		foreach (explode('#', $a->anwser) as $anwser)
		    array_push($intarr, array_search($anwser, explode('#', $a->choices)));
		$a->anwser = $intarr;
	    }
	    unset($a->type);
	    unset($a->choices);
	    $assocanwsers = CommonModel::array_push_assoc($assocanwsers, $a->Question_id, $a->anwser);
	}
	return $assocanwsers;
    }

    public static function getRealEndTime($aid) {
	$assignment = AssignmentModel::getAssignment($aid);
	$startTime = new DateTime(AssignmentModel::getStartTime($aid));
	date_add($startTime, date_interval_create_from_date_string($assignment->timelimit . ' mins'));

	if ($assignment->timelimit > 0)
	    return min(new DateTime($assignment->duedate), $startTime);
	else
	    return new DateTime($assignment->duedate);
    }

    // time reserve in seconds
    public static function canSolve($aid, $reserve = 0) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	$pts = dibi::fetchSingle('SELECT points FROM onlinesubmission WHERE User_id=%i AND Assignment_id=%i', $uid, $aid);
	$hasPoints = (($pts >= 0) && ($pts != FALSE));
	$assignment = AssignmentModel::getAssignment($aid);
	$now = new DateTime;
	$end = self::getRealEndTime($aid);
	date_add($end, date_interval_create_from_date_string($reserve . ' seconds'));
	return ($now > new DateTime($assignment->assigndate) && $now < $end && !$hasPoints);
    }

    public static function getStartTime($aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	return dibi::fetchSingle('SELECT started FROM onlinesubmission WHERE Assignment_id=%i AND User_id=%i', $aid, $uid);
    }

    public static function isSolved($aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	return (null != dibi::fetch('SELECT * FROM onlinesubmission WHERE Assignment_id=%i AND User_id=%i', $aid, $uid));
    }

    public static function getRadioAnwserPos($question, $value) {
	return array_search($value, explode('#', $question->choices));
    }

    public static function getMultiAnwserArray($question, $anwser) {
	$intarr = array();
	foreach (explode('#', $anwser) as $oneanwser)
	    array_push($intarr, array_search($oneanwser, explode('#', $question->choices)));
	return $intarr;
    }

    public static function getAnwserFile($afid) {
	$r = dibi::fetch('SELECT * FROM anwser 
	    JOIN question ON Question_id=question.id 
	    JOIN assignment ON Assignment_id=assignment.id 
	    JOIN course ON Course_id=course.id 
	    JOIN user ON User_id=user.id 
		WHERE type=\'file\' AND anwser=%s', $afid);
	$r->filename = dibi::fetchSingle('SELECT filename FROM anwserfile WHERE id=%i', $afid);

	return $r;
    }

    public static function sendNewAssignmentNotif($aid) {
	$assignment = self::getAssignment($aid);
	$course = CourseModel::getCourseByID($assignment->Course_id);
	$subject = 'New Assignment added to ' . $course->name;
	$msg = 'There is a new assignment called ' . $assignment->name . ' in your course <b>' . $course->name . '</b><br />
	    You can check it at <a href="' . MailModel::$hostUrl . '">' . MailModel::$hostUrl . '</a>.';

	MailModel::sendMailToStudents($course->id, $subject, $msg);
    }

    public static function sendAssignmentNotifications() {
	$assignments = dibi::fetchAll('SELECT * FROM assignment WHERE duedate>NOW()');
	foreach ($assignments as $assignment) {
	    $course = CourseModel::getCourseByID($assignment->Course_id);
	    $students = CourseModel::getStudents($assignment->Course_id);
	    foreach ($students as $student) {
		$settings = SettingsModel::getSettings($student->id);
		$dayInterval = $settings->assignment_notif_interval;
		$due = new DateTime($assignment->duedate);
		$tmp = date_add(new DateTime,  date_interval_create_from_date_string($dayInterval.' days'));
		$tmp2 = date_add(new DateTime,  date_interval_create_from_date_string(($dayInterval+1).' days'));
		if ($tmp<$due && $tmp2>$due){
		    $subject = 'Notification of upcoming assignment duedate';
		    $msg = 'Assignment <b>'.$assignment->name.'</b> duedate is on <b>'.$assignment->duedate.'</b>. You can check it at <a href="' . MailModel::$hostUrl . '">' . MailModel::$hostUrl . '</a>.';
		    MailModel::addMail($student->email, $subject, $msg);
		}
		    
		
	    }
	}
    }

}

?>
