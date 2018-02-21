<?php

class Comment {

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param userid - user who add's a new comment
     * @param videoid - video which the comment is attached to
     * @param comment - actual comment text
     * @return commentid - id of newly created comment
     */
    public static function add($db, $userid, $videoid, $comment) {
        $commentid = uniqid(rand(10000,99999), true);

        $sql = "INSERT INTO Comment (id, userid, videoid, comment) VALUES (?, ?, ?, ?)";
        $stmt = $db->prepare($sql);

        $param = array($commentid, $userid, $videoid, $comment);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {
            return 0;
        }
        return $commentid;
    }

    /* 
     * @requires login
     * @param db - PDO connection object
     * @param commentid - comment we want to delete
     */
    public static function delete($db, $commentid) {
        $sql = "DELETE FROM Comment WHERE id=?";
        $sth = $db->prepare($sql);
        $param = array($commentid);
        $sth->execute($param);

        return ($sth->rowCount() === 1);
    }


    /* 
     * @param db - PDO connection object
     * @param videoid - video we want comments from
     * @return array of comments
     */
    public static function get($db, $videoid) {
        $sql = "SELECT comment.id, userid, videoid, comment, comment.time, fullname
                FROM Comment
                INNER JOIN User ON comment.userid = user.id
                WHERE videoid=(?) ORDER BY time DESC";

        $stmt = $db->prepare($sql);
        $param = array($videoid);
        $stmt->execute($param);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
