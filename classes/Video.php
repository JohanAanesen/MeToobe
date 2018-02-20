<?php

/**
  *  class Video
  */
class Video {

    public static function add($db, $uid, $name, $descr, $mime, $size) {
        $sql = "INSERT INTO video (id, userid, name, description, mime, views) VALUES (:id, :user, :name, :description, :mime, 0)";
        $sth = $db->prepare ($sql);

      //  $videoid = (string)md5($size . $name . $mime . $uid);
        $videoid = uniqid();
        $sth->bindParam(':id', $videoid);
        $sth->bindParam(':user',  $uid);
        $sth->bindParam(':name',  $name);
        $sth->bindParam(':description', $descr);
        $sth->bindParam(':mime', $mime);
        $sth->execute();

        if ($sth->rowCount() !== 1) {
            return 0;
        }
        return $videoid;
    }

    public static function delete($db, $videoid) {
        $sql = "DELETE FROM video WHERE id=:videoid";
        $sth = $db->prepare($sql);
        $sth->bindParam(':videoid', $videoid);
        $sth->execute();
    }

    /**
      * Saves a video to the filesystem.
      */
    public static function saveToFile($uid, $videoid, $tmp_filepath, $mime) {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];

        if (!file_exists("$ROOT/uploadedFiles")){
            @mkdir("$ROOT/uploadedFiles");
        }

        $upload_dir = "$ROOT/uploadedFiles/$uid";
        $extension = "";
        // CREATE DIRECTORY IF DOES NOT EXIST
        if (!file_exists($upload_dir)) { // Brukeren har ikke lastet opp filer tidligere
            @mkdir($upload_dir);
        }

        // Add extension to the video, to know how to play it back.
        if ($mime == "video/mp4") {
            $extension .= "mp4";
        } else if($mime == "video/webm") {
            $extension .= "webm";
        } else if($mime == "video/ogg") {
            $extension .= "ogg";
        }

        // MOVE FILE FROM TEMP-DIRECTORY to UPLOAD-DIRECTORY
        if (@move_uploaded_file($tmp_filepath, "$upload_dir/$videoid.$extension")) {
            return $uid;
        }
        return 0;
    }

    /**
     * @param $videoid
     * @return array|null
     */
    public static function findVideo($db, $videoid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM video WHERE id = (?)";
            $param = array($videoid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()==1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                if($row){
                    return $row;
                }
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /**
     * @param $db
     * @return array|null
     */
    public static function getNewVideos($db){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT id, name FROM video ORDER BY time DESC LIMIT 6";
            $param = array();
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                $videos = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $videos[] = $row;
                }
                return $videos;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }


    /**
     * @param $videoid
     * @return bool
     */
    public static function viewCountPlus($db, $videoid){
        try{
            //SQL Injection SAFE query method:
            $query = "UPDATE video SET views=views+1 WHERE id = (?)";
            $param = array($videoid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()==1) {
                return true;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return false;
    }

    /**
     * @param $db
     * @param $videoid
     * @return null
     */
    public static function findLikes($db, $videoid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM userlike WHERE videoid = (?)";
            $param = array($videoid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                $users = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $users[] = $row;
                }
                return $users;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /**
     * @param $db
     * @param $videoid
     * @param $userid
     * @param $like
     */
    public static function videoVote($db, $videoid, $userid, $like){
        try{
            //SQL Injection SAFE query method:
            $query = "INSERT INTO userlike (userid, videoid, vote) VALUES (?, ?, ?)";
            $param = array($userid, $videoid, $like);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
    }


    public static function searchVideos($db, $q){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT video.id, video.name FROM video
                      INNER JOIN user ON video.userid = user.id
                      WHERE video.name LIKE (?)
                      OR user.fullname LIKE (?)
                      OR user.email LIKE (?)
                      OR video.course LIKE (?)
                      OR video.topic LIKE (?)
                      LIMIT 10";

            //adding the wildcard characters to query word
            $qWild = "%".$q."%";

            $param = array($qWild, $qWild, $qWild, $qWild, $qWild);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                $videos = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $videos[] = $row;
                }
                return $videos;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

};
