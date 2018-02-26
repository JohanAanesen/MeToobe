<?php


class User {

    private static $KEY_SESSION_USERID = 'userid';
    private static $KEY_SESSION_USERTYPE = 'usertype';

    /**
     * @function create
     *  @brief  Create a valid new user in the database.
     *  @param  db:             PDOConnection
     *  @param  fullname:       string - Example: Jonas Testerson
     *  @param  email:          string - Example: jonas.test@gmail.com
     *  @param  password:       string - Example: "my secret awesome password is magic"
     *  @param  wannebeTeacher: bool   - The user has requested the teacher role. An administrator has to
     *                                   approve of the request, before the user gets the teacher role.
     *  @return userid | 0
     */
    static function create($db, $fullname, $email, $password, $wannebeTeacher) {

        // Is email already registered?
        $query = "SELECT * FROM User WHERE email = ?";
        $param = array($email);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        // @error A user with that email already registered.
        if ($stmt->fetchColumn() > 0) {
            return 0;
        }
        $query = "INSERT INTO user (id, fullname, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?, ?)";
        $userid = uniqid();
        $param = array($userid, $fullname, $email, md5($password), "student", $wannebeTeacher);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        // @error There was an error when inserting the user
        if ($stmt->rowCount() !== 1) {
            return 0;
        }
        return $userid;
    }

    /**
      * @function login
      *  @param  db:       PDOConnection
      *  @param  email:    string - Example: jonas.test@gmail.com
      *  @param  password: string - Example: "my secret awesome password is magic"
      *  @global $_SESSION
      *  @return userid | 0
      */
    static function login($db, $email, $password) {

        $query = "SELECT * FROM user WHERE email = (?) AND password = (?)";
        $param = array($email, md5($password));
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        if($stmt->rowCount() !== 1) {
            return 0;
        }

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        User::requireSession();
        $_SESSION[User::$KEY_SESSION_USERID] = $row['id'];
        $_SESSION[User::$KEY_SESSION_USERTYPE] = $row['usertype'];

        return $_SESSION[User::$KEY_SESSION_USERID];
    }

    /**
      * @function logout
      * @brief Modified the $_SESSION to in effect log out the user.
      * @global $_SESSION
      */
    static function logout() {
        User::requireSession();

        unset($_SESSION[User::$KEY_SESSION_USERID]);
        unset($_SESSION[User::$KEY_SESSION_USERTYPE]);
    }

    /**
      * @function getLoggedInUserid
      * @global $_SESSION
      * @return userid | 0
      */
    static function getLoggedInUserid() {
        User::requireSession();

        if( !isset($_SESSION[User::$KEY_SESSION_USERID]) ) {
            return 0;
        }
        return $_SESSION[User::$KEY_SESSION_USERID];
    }

    /**
     * @global $_SESSION
     */
    static function isAdmin() {
        User::requireSession();

        if ( !isset($_SESSION[User::$KEY_SESSION_USERTYPE]) || $_SESSION[User::$KEY_SESSION_USERTYPE] !== 'admin' )
            return 0;
        return true;
    }

    /**
     * @function updateUser
     * @param db: PDOConnection
     * @param userid: string
     * @param password: string
     * @param usertype: enum['admin', 'teacher', 'student']
     * @param wannabe:  bool
     * @return if updated true | false
     */
    static function updateUser($db, $userid, $password, $usertype, $wannabe) {

        $query = "UPDATE user SET password = (?), usertype = (?), wannabe = (?) WHERE id = (?)";
        $param = array($password, $usertype, $wannabe, $userid);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return ($stmt->rowCount() === 1);
    }

    /**
     * @function updateType
     * @param db: PDOConnection
     * @param userid: string
     * @param usertype: enum['admin', 'teacher', 'student']
     * @return if updated true | false
     */
    static function updateType($db, $userid, $usertype) {

        $query = "UPDATE user SET usertype = ?, wannabe = ? WHERE id = (?)";
        $param = array($usertype, false, $userid);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return ($stmt->rowCount() == 1);
    }

    /**
     * @function getWannabeTeachers
     * @requires admin rights
     * @brief getWannabe grabs all wannabe's from the DB
     * @param db: PDOConnection
     * @return array of wannabeTeachers | null
     */
    static public function getWannabeTeachers($db){
        // @error - user is not admin
        if ( !User::isAdmin() ) {
            return null;
        }
        //SQL Injection SAFE query method:
        $query = "SELECT * FROM user WHERE wannabe = (?)";
        $param = array(true);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @function get
     * @param db: PDOConnection
     * @return user | null
     */
    static public function get($db, $userid) {
        //SQL Injection SAFE query method:
        $query = "SELECT * FROM user WHERE id = (?)";
        $param = array($userid);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    static private function requireSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }
}
