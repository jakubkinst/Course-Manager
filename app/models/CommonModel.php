<?php

/**
 * Model class with static methods that helps all across the application
 * 
 * Responsible mostly for communication with database via dibi.
 *
 * @author     Jakub Kinst <jakub@kinst.cz> (@link http://jakub.kinst.cz)
 * @package    Course-Manager/Models/Tools
 */
class CommonModel extends Object {

    /**
     * Returns relative date format
     * e.g. Tomorrow, 5 days ago, in 20 days, ...
     * @param DateTime $time
     * @return string 
     */
    public static function relative_date($time) {
	$today = strtotime(date('M j, Y'));
	$reldays = ($time - $today) / 86400;
	if ($reldays >= 0 && $reldays < 1) {
	    return _('Today');
	} else if ($reldays >= 1 && $reldays < 2) {
	    return _('Tomorrow');
	} else if ($reldays >= -1 && $reldays < 0) {
	    return _('Yesterday');
	}

	if (abs($reldays) < 7) {
	    if ($reldays > 0) {
		$reldays = floor($reldays);
		return _('In ') . $reldays . _(' day') . ($reldays != 1 ? 's' : '');
	    } else {
		$reldays = abs(floor($reldays));
		return $reldays . _(' day') . ($reldays != 1 ? 's' : '') . _(' ago');
	    }
	}

	if (abs($reldays) < 182) {
	    return date('l, j F', $time ? $time : time());
	} else {
	    return date('l, j F, Y', $time ? $time : time());
	}
    }

    /**
     * Converts a string date to a php date format
     * @param string $formDate
     * @return date 
     */
    public static function convertFormDate($formDate) {
	$phpdate = strtotime($formDate);
	return date('Y-m-d H:i:s', $phpdate);
    }

    /**
     * Push an associative pair into array
     * @param array $array
     * @param type $key
     * @param type $value
     * @return array 
     */
    public static function array_push_assoc($array, $key, $value) {
	$array[$key] = $value;
	return $array;
    }

    /**
     * Extracts all strings from an application and saves them into .po file for 
     * further translation. Run only when strings have changed.
     */
    public static function getTextExtract() {
	@$ge = new NetteGettextExtractor(); // provede základní nastavení pro šablony apod.
	@$ge->setupForms()->setupDataGrid(); // provede nastavení pro formuláře a DataGrid
	@$ge->scan(APP_DIR); // prohledá všechny aplikační soubory
	@$ge->save(APP_DIR . '/locale/en.po'); // vytvoří Gettextový soubor editovatelný např v Poeditu
    }

}

?>
