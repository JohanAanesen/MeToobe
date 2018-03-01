<?php

/**
  *  class Video
  */
class Video {

    /**
     * @function add
     * @brief adds video to DB
     * @param $db
     * @param $uid
     * @param $name
     * @param string $descr
     * @param string $mime
     * @param string $thumbnail
     * @return int|string
     */
    public static function add($db, $uid, $name, $descr="", $mime="", $thumbnail="") {
        $videoid = uniqid();
        $sql = "INSERT INTO"
             ." video   (     id,userid, name, description, mime, thumbnail)"
             . "VALUES (       ?,     ?,    ?,         ?,           ?,    ?)";
        $param = array($videoid,   $uid,$name,     $descr, $mime, $thumbnail);
        $sth = $db->prepare ($sql);
        $sth->execute($param);

        if ($sth->rowCount() !== 1) {
            return 0;
        }
        return $videoid;
    }


    /**
     * @function delete
     * @brief deletes video and the videos comments, likes and entries in playlists from DB
     * @param $db
     * @param $videoid
     * @return bool
     */
    public static function delete($db, $videoid) {
        $db->beginTransaction();
        try {
            $sql = 'DELETE FROM comment WHERE videoid = ?';             //deletes all comments on the video
            $stmt = $db->prepare($sql);
            $param = array($videoid);
            $stmt->execute($param);

            $sql = 'DELETE FROM userlike WHERE videoid = ?';            //deletes all likes/dislikes on the video
            $stmt = $db->prepare($sql);
            $param = array($videoid);
            $stmt->execute($param);

            $sql = 'DELETE FROM videoplaylist WHERE videoid = ?';       //deletes all entries of video in playlists
            $stmt = $db->prepare($sql);
            $param = array($videoid);
            $stmt->execute($param);

            $sql = 'DELETE FROM video WHERE id = ?';                    //deletes video
            $stmt = $db->prepare($sql);
            $param = array($videoid);
            $stmt->execute($param);

        } catch (PDOException $e) {
            print_r($e->errorInfo);
            $db->rollBack();
            return false;
        }
        $db->commit();
        return true;
    }

    /**
     * @function saveToFile
     * @brief Saves a video to the filesystem.
     * @param $uid
     * @param $videoid
     * @param $tmp_filepath
     * @param $mime
     * @return int
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
        } else {
            return 0;
        }

        // MOVE FILE FROM TEMP-DIRECTORY to UPLOAD-DIRECTORY
        if (@move_uploaded_file($tmp_filepath, "$upload_dir/$videoid.$extension")) {
            return $uid;
        }
        return 0;
    }

    /**
     * @function get
     * @brief retrieves everything about $videoid from DB
     * @param $videoid
     * @return array|null
     */
    public static function get($db, $videoid){
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
     * @function getNewVideos
     * @brief Retrieves the newest videos from DB, ordered by time
     * @param $db
     * @return array|null
     */
    public static function getNewVideos($db){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT id, name, thumbnail FROM video ORDER BY time DESC LIMIT 6";
            $param = array();
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }



    /**
     * @function viewCountPlus
     * @brief Increases $videoid's count by 1 in DB
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
     * @function findLikes
     * @retrieves all likes on $videoid from DB
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
                $likes = array();
                while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $likes[] = $row;
                }
                return $likes;
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /**
     * @function updateLike
     * @brief Changes the user's like to opposite or deletes it in DB
     * @param $db
     * @param $videoid
     * @param $userid
     * @param $vote
     * @return bool
     */
    public static function updateLike($db, $videoid, $userid, $vote){
        $db->beginTransaction();

        try{
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM userlike WHERE videoid = (?) AND userid = (?)";
            $param = array($videoid, $userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() == 1){
                $row = $stmt->fetch(PDO::FETCH_ASSOC);

                if($vote == 'up'){
                    if($row['vote'] == 1){
                        self::deleteLike($db, $videoid, $userid);
                    }else if($row['vote'] == 0){
                        self::changeLike($db, $videoid, $userid, 1);
                    }
                }else if ($vote == 'down'){
                    if($row['vote'] == 0){
                        self::deleteLike($db, $videoid, $userid);
                    }else if($row['vote'] == 1){
                        self::changeLike($db, $videoid, $userid, 0);
                    }
                }else{
                    $db->rollBack();
                    return false;
                }


            }else{
                $db->rollBack();
                return false;
            }

        }catch(PDOException $ex){
            echo "Something went wrong ".$ex; //Error message
            $db->rollBack();
            return false;
        }

        $db->commit();
        return true;
    }

    /**
     * @function changeLike
     * @brief Changes like to $vote (1 or 0) in DB
     * @param $db
     * @param $videoid
     * @param $userid
     * @param $vote
     */
    public static function changeLike($db, $videoid, $userid, $vote){
        try{
            //SQL Injection SAFE query method:
            $query = "UPDATE userlike SET vote = (?) WHERE videoid = (?) AND userid = (?)";
            $param = array($vote, $videoid, $userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);
        }catch(PDOException $ex){
            echo "Something went wrong ".$ex; //Error message
        }
    }

    /**
     * @function deleteLike
     * @brief deletes like from DB
     * @param $db
     * @param $videoid
     * @param $userid
     */
    public static function deleteLike($db, $videoid, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "DELETE FROM userlike WHERE videoid = (?) AND userid = (?)";
            $param = array($videoid, $userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);
        }catch(PDOException $ex){
            echo "Something went wrong ".$ex; //Error message
        }
    }

    /**
     * @function videoVote
     * @brief Inserts new like to DB
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

    /**
     * @function searchVideos
     * @brief MySQL query to search the video part of DB for anything related to $q
     * @param $db
     * @param $q
     * @return array|null
     */
    public static function searchVideos($db, $q){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT video.id, video.name, video.description, video.thumbnail FROM video
                      INNER JOIN user ON video.userid = user.id
                      WHERE video.name LIKE (?)
                      OR video.description LIKE (?)
                      OR user.fullname LIKE (?)
                      OR user.email LIKE (?)
                      LIMIT 10";

            //adding the wildcard characters to query word
            $qWild = "%".$q."%";

            $param = array($qWild, $qWild, $qWild, $qWild);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount() > 0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /**
     * @function getUsersVideos
     * @brief Retrieves $userid's videos from DB
     * @param $db
     * @param $userid
     * @return array|null
     */
    public static function getUsersVideos($db, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT video.id, video.name, video.thumbnail FROM video
                      WHERE video.userid LIKE (?)
                      LIMIT 10";


            $param = array($userid);
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
     * @function getSubscribedVideos
     * @brief Retrieves all videos that are part of a playlist $userid subscribes to, from DB
     * @param $db
     * @param $userid
     * @return null
     */
    public static function getSubscribedVideos($db, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT video.id, video.name, video.thumbnail FROM video
                      INNER JOIN videoplaylist ON video.id = videoplaylist.videoid
                      INNER JOIN playlist ON videoplaylist.playlistid = playlist.id
                      INNER JOIN usersubscribe ON playlist.id = usersubscribe.playlistid
                      INNER JOIN user ON usersubscribe.userid = user.id
                      WHERE user.id LIKE (?)
                      ORDER BY video.time DESC
                      LIMIT 6";
            $param = array($userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if ($stmt->rowCount()>0) {
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Something went wrong".$ex; //Error message
        }
        return null;
    }

    /**
     * @function updateVideoTitleDescription
     * @brief Updates the Video title and description
     * @param $db
     * @param $videoid
     * @param $newName
     * @param $newDesc
     * @return bool
     */
    public static function updateVideoTitleDescription($db, $videoid, $newName, $newDesc){
        $sql = "UPDATE video SET name = ?, description = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $param = array($newName, $newDesc, $videoid);
        $stmt->execute($param);

        return ($stmt->rowCount() === 1);
    }

};
