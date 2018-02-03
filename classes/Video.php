<?php

/**
  *  class Video
  */
class Video {
    private $db;

    function __construct($db) {
        $this->db = $db;
    }

    function add($uid, $name, $descr, $mime, $size) {
        $sql = "INSERT INTO video (videoid, user, name, descr, mime, views) VALUES (:videoid, :user, :name, :descr, :mime, 0)";
        $sth = $this->db->prepare ($sql);

      //  $videoid = (string)md5($size . $name . $mime . $uid);
        $videoid = uniqid();
        $sth->bindParam(':videoid', $videoid);
        $sth->bindParam(':user',  $uid);
        $sth->bindParam(':name',  $name);
        $sth->bindParam(':descr', $descr);
        $sth->bindParam(':mime', $mime);
        $sth->execute();
        
        if ($sth->rowCount() === 1) {  
            assert($videoid === $this->db->lastInsertId());
            return $videoid;
        }
        return 0;
    }

    function delete($videoid) {
        $sql = "delete from video where videoid=:videoid";
        $sth = $this->db->prepare($sql);
        $sth->bindParam(':videoid', $videoid);
        $sth->execute();
    }

    /**
      * Saves a video to the filesystem.
      */
    function saveToFile($uid, $videoid, $tmp_filepath, $mime) {
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
    public function findVideo($videoid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM video WHERE videoid = (?)";
            $param = array($videoid);
            $stmt = $this->db->prepare($query);
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
     * @param $videoid
     * @return bool
     */
    public function viewCountPlus($videoid){
        try{
            //SQL Injection SAFE query method:
            $query = "UPDATE video SET views=views+1 WHERE videoid = (?)";
            $param = array($videoid);
            $stmt = $this->db->prepare($query);
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
            $query = "SELECT * FROM userlike WHERE video = (?)";
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
            $query = "INSERT INTO userlike (userid, video, vote) VALUES (?, ?, ?)";
            $param = array($userid, $videoid, $like);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
    }

};
