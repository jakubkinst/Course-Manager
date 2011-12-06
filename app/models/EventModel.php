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
        $values['date'] = CommonModel::convertFormDate($values['date']);
        return dibi::query('INSERT INTO event', $values);
    }
    
     public static function editEvent($eid,$values) {
	 $array = array(
	     'name' => $values['name'],
	     'date' => CommonModel::convertFormDate($values['date']),
	     'description' => $values['description'],
	 );
        return dibi::query('UPDATE event SET', $array,'WHERE id=%i',$eid);
    }
    
    public static function deleteEvent($eid) {
        return dibi::query('DELETE FROM event WHERE id=%i',$eid);
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
