<?php

/**
 * AssignmentModel
 *
 * @author Jakub Kinst
 */
class AssignmentModel extends Object {

    public static function addNormalAssignment($values, $cid) {
	$values['Course_id'] = $cid;
	$values['created'] = new DateTime;
	$values['assigndate'] = CommonModel::convertFormDate($values['assigndate']);
	$values['duedate'] = CommonModel::convertFormDate($values['duedate']);
	if (dibi::query('INSERT INTO assignment', $values))
	    return dibi::getInsertId();
	else
	    return -1;
    }

    public static function addText($label, $aid) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'text', 'label' => $label));
    }

    public static function addTextArea($label, $aid) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'textarea', 'label' => $label));
    }

    public static function addRadio($label, $choices, $aid) {

	function isEmpty($var) {
	    return(!($var == ''));
	}

	$choices = array_filter($choices, 'isEmpty');
	$choices2 = implode('#', $choices);
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'radio', 'label' => $label, 'choices' => $choices2));
    }

    public static function addMultiSelect($label, $choices, $aid) {

	function isEmpty($var) {
	    return(!($var == ''));
	}

	$choices = array_filter($choices, 'isEmpty');
	$choices2 = implode('#', $choices);
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'multi', 'label' => $label, 'choices' => $choices2));
    }


    public static function getQuestions($aid) {
	$q = dibi::fetchAll('SELECT * FROM question WHERE Assignment_id=%i', $aid);
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
	return dibi::query('DELETE FROM question WHERE id=%i', $qid);
    }

    public static function getCourseIDByAssignmentID($aid) {
	return dibi::fetchSingle('SELECT Course_id FROM assignment WHERE id=%i', $aid);
    }

    public static function getCourseIDByQuestionID($qid) {
	return dibi::fetchSingle('SELECT Course_id FROM question WHERE id=%i', $qid);
    }

    public static function getAssignment($aid) {
	$q = dibi::fetch('SELECT * FROM assignment WHERE id=%i', $aid);
	return $q;
    }

    public static function getAssignments($cid) {
	$q = dibi::fetchAll('SELECT * FROM assignment WHERE Course_id=%i', $cid);
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
		$result = true;
		if (null == dibi::fetch('SELECT * FROM anwser WHERE User_id=%i AND Question_id=%i', $uid, $key))
		    $result = dibi::query('INSERT INTO anwser', array('User_id' => $uid, 'Question_id' => $key, 'anwser' => $value));
		else
		    dibi::query('UPDATE anwser SET', array('anwser' => $value),'WHERE User_id=%i AND Question_id=%i',$uid,$key);
		if (!$result)
		    $ok = false;
	    }
	}

	return $ok;
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
    
    public static function getRealEndTime($aid){
	$assignment = AssignmentModel::getAssignment($aid);
	$startTime = new DateTime(AssignmentModel::getStartTime($aid));
	date_add($startTime, date_interval_create_from_date_string($assignment->timelimit.' mins'));
	
	if ($assignment->timelimit>0)
	    return min(new DateTime($assignment->duedate),$startTime);
	else
	    return new DateTime($assignment->duedate);
    }
    // time reserve in seconds
    public static function canSolve($aid,$reserve = 0){	
	$assignment = AssignmentModel::getAssignment($aid);
	$now = new DateTime;
	$end = self::getRealEndTime($aid);
	date_add($end, date_interval_create_from_date_string($reserve.' seconds'));
	return ($now > new DateTime($assignment->assigndate) && $now < $end);
    }

    public static function getStartTime($aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	return dibi::fetchSingle('SELECT started FROM onlinesubmission WHERE Assignment_id=%i AND User_id=%i', $aid, $uid);
    }

    public static function isSolved($aid) {
	$uid = UserModel::getUserID(Environment::getUser()->getIdentity());
	return (null != dibi::fetch('SELECT * FROM onlinesubmission WHERE Assignment_id=%i AND User_id=%i', $aid, $uid));
    }

}

?>
