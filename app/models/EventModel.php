<?php

/**
 * Description of EventModel
 *
 * @author JerRy
 */
class EventModel extends Object {

    public static function addEvent($values, $cid) {
        $values['added'] = new DateTime;
        $values['Course_id'] = $cid;
        $phpdate = strtotime( $values['date'] );
        $values['date'] = date( 'Y-m-d', $phpdate );
        return dibi::query('INSERT INTO event', $values);
    }


    public static function getEvents($cid) {
        $events = dibi::fetchAll('SELECT * FROM event WHERE Course_id=%i ORDER BY date ASC',$cid);        
        return $events;
    }

    public static function getEvent($eid) {
        $topic = dibi::fetch('SELECT * FROM event WHERE id=%i', $eid);        
        return $topic;
    }

    public static function getCourseIDByEventID($eid){
        return dibi::fetchSingle('SELECT Course_id FROM event WHERE id=%i',$eid);
    }
}

?>
