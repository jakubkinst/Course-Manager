<?php

/**
 * Description of ForumModel
 *
 * @author JerRy
 */
class ForumModel extends Object {

    public static function addTopic($values, $cid) {
        $values['created'] = new DateTime;
        $values['Course_id'] = $cid;
        $values['User_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        return dibi::query('INSERT INTO topic', $values);
    }

    public static function addReply($values, $tid) {
        $values['created'] = new DateTime;
        $values['Topic_id'] = $tid;
        $values['User_id'] = UserModel::getUserID(Environment::getUser()->getIdentity());
        return dibi::query('INSERT INTO reply', $values);
    }

    public static function getTopics($cid, $offset, $limit) {
        $topics = dibi::fetchAll('SELECT * FROM topic WHERE Course_id=%i LIMIT %i OFFSET %i', $cid, $limit, $offset);
        foreach ($topics as $topic) {
            $topic['author'] = UserModel::getUser($topic['User_id']);
            $topic['replies'] = ForumModel::countReplies($topic['id']);
            $topic['lastreply'] = ForumModel::getLastReply($topic['id']);
            if ($topic['lastreply']==null)
                $topic['lastreply'] = $topic;
        }
        return $topics;
    }

    public static function getTopic($tid) {
        $topic = dibi::fetch('SELECT * FROM topic WHERE id=%i', $tid);
        $topic['author'] = UserModel::getUser($topic['User_id']);
        return $topic;
    }

    public static function countReplies($tid) {
        return dibi::fetchSingle('SELECT COUNT(*) FROM reply WHERE Topic_id=%i', $tid);
    }
    
    public static function countTopics($cid) {
        return dibi::fetchSingle('SELECT COUNT(*) FROM topic WHERE Course_id=%i', $cid);
    }

    public static function getReplies($tid, $offset, $limit) {
        $replies = dibi::fetchAll('SELECT * FROM reply WHERE Topic_id=%i LIMIT %i OFFSET %i', $tid, $limit, $offset);
        foreach ($replies as $reply) {
            $reply['author'] = UserModel::getUser($reply['User_id']);
        }
        return $replies;
    }

    public static function getLastReply($tid) {
        return dibi::fetch('SELECT * FROM reply WHERE Topic_id=%i ORDER BY created DESC LIMIT 1', $tid);
    }

    public static function getCourseIDByTopicID($tid){
        return dibi::fetchSingle('SELECT Course_id FROM topic WHERE id=%i',$tid);
    }
}

?>
