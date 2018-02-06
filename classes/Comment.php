<?php

class Comment {
    public static function add($db, $userid, $videoid, $comment) {
        $commentid = uniqid();

        $sql = "INSERT INTO comment (commentid, user, video, comment) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $param = array($commentid, $userid, $videoid, $comment);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {  
            return 0;
        }
        return $commentid;
    }

    public static function delete($db, $commentid) {
        $sql = "DELETE FROM comments WHERE commentid=?";
        $sth = $db->prepare($sql);
        $param = array($commentid);
        $sth->execute($commentid);
    }
}