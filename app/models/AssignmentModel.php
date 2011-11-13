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
	else return -1;
	
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

    public static function addCheckbox($label, $aid) {
	dibi::query('INSERT INTO question', array('Assignment_id' => $aid, 'type' => 'checkbox', 'label' => $label));
    }

    public static function getQuestions($aid) {
	$q = dibi::fetchAll('SELECT * FROM question WHERE Assignment_id=%i', $aid);
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
}

?>
