<?php

class Comment {
    public static function add($db, $userid, $videoid, $comment) {
        $commentid = uniqid();

        $sql = "INSERT INTO Comments (commentid, user, video, comment) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $param = array($commentid, $userid, $videoid, $comment);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {  
            return 0;
        }
        return $commentid;
    }

    public static function delete($db, $commentid) {
        $sql = "DELETE FROM Comments WHERE commentid=?";
        $sth = $db->prepare($sql);
        $param = array($commentid);
        $sth->execute($param);
    }

    public static function get($db, $videoid) {
        $sql = "SELECT * FROM Comments WHERE video=? ORDER BY time DESC"; 
        
        $stmt = $db->prepare($sql);
        $param = array($videoid);
        $stmt->execute($param);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}