<?php

/**
 * Description of ResourceModel
 *
 * @author JerRy
 */
class ResourceModel extends Object {

    public static $UPLOAD_DIR = "uploads";

    public static function uploadFile($values) {

        $file = $values['file'];
        $values['size'] = $file->getSize();
        $hashfilename = md5($_SERVER["REMOTE_ADDR"] . time()) . "_" . $file->getName();
        if ($file->isOK()) {
            if ($file->move(WWW_DIR . "/../" . ResourceModel::$UPLOAD_DIR . "/" . $hashfilename)) {
                $values['filename'] = $hashfilename;
                unset($values['file']);
                dibi::query('INSERT INTO resource', $values);
                return true;
            }
        }
        return false;
    }

    public static function getFiles($cid) {
        $files = dibi::fetchAll('SELECT * FROM resource WHERE Course_id=%i', $cid);
        foreach ($files as $file) {
            $tmp = $file->filename;
            $file->filename = BASE_DIR . "/" . ResourceModel::$UPLOAD_DIR . "/" . $tmp;
        }
        return $files;
    }

}

?>
