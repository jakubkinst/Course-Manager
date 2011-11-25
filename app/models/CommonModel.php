<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CommonModel
 *
 * @author JerRy
 */
class CommonModel extends Object {

   public static function relative_date($time) {
	$today = strtotime(date('M j, Y'));
	$reldays = ($time - $today) / 86400;
	if ($reldays >= 0 && $reldays < 1) {
	    return 'Today';
	} else if ($reldays >= 1 && $reldays < 2) {
	    return 'Tomorrow';
	} else if ($reldays >= -1 && $reldays < 0) {
	    return 'Yesterday';
	}

	if (abs($reldays) < 7) {
	    if ($reldays > 0) {
		$reldays = floor($reldays);
		return 'In ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
	    } else {
		$reldays = abs(floor($reldays));
		return $reldays . ' day' . ($reldays != 1 ? 's' : '') . ' ago';
	    }
	}

	if (abs($reldays) < 182) {
	    return date('l, j F', $time ? $time : time());
	} else {
	    return date('l, j F, Y', $time ? $time : time());
	}
    }

    public static function convertFormDate($formDate) {
	$phpdate = strtotime($formDate);
	return date('Y-m-d H:i:s', $phpdate);
    }

    public static function array_push_assoc($array, $key, $value) {
	$array[$key] = $value;
	return $array;
    }

    public static function getTextExtract() {
	@$ge = new NetteGettextExtractor(); // provede základní nastavení pro šablony apod.
	@$ge->setupForms()->setupDataGrid(); // provede nastavení pro formuláře a DataGrid
	@$ge->scan(APP_DIR); // prohledá všechny aplikační soubory
	@$ge->save(APP_DIR . '/locale/en.po'); // vytvoří Gettextový soubor editovatelný např v Poeditu
    }

}

?>
