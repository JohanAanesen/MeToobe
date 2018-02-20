<?php


class User {
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
        
        $query = "INSERT INTO user (id, fullname, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?, ?)";
        $param = array(uniqid(), $fullname, $email, md5($password), "student", $wannabe);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        if ($stmt->rowCount() !== 1) {
            return 0;
        }
        return $db->lastInsertId();        
    }

    /**
      * @function login
      *  @param  db:       PDOConnection
      *  @param  email:    string - Example: jonas.test@gmail.com
      *  @param  password: string - Example: "my secret awesome password is magic"
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
        $_SESSION['userid'] = $row['id'];

        return $_SESSION['userid'];
    }
    
    /**
      * @function logout
      * @brief Modified the $_SESSION to in effect log out the user.
      */
    static function logout() {
        unset($_SESSION['userid']);
    }

    /**
      * @function isLoggedIn
      * @param userid: string
      * @return userid | 0
      */
    static function isLoggedIn($userid) {
        if( !isset($_SESSION['userid']) ) {
            return 0;
        }
        return $_SESSION['userid'];
    }

    /** 
      * @function isLoggedInOrRedirectTo
      * @param userid: string
      * @param redirectURL: string - URL to redirect to if there is an error
      * @return  userid | redirect to specified redirectURL
      */
    static function isLoggedInOrRedirectTo($userid, $redirectURL) {
    
        if( !isset($_SESSION['userid']) ) {
            header("Location: " . $redirectURL);
            exit();
        }
        return $_SESSION['userid'];
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
     * @function updateUserType
     * @param db: PDOConnection
     * @param userid: string
     * @param usertype: enum['admin', 'teacher', 'student']
     * @return if updated true | false 
     */
    static function updateUserType($db, $userid, $usertype) {

        $query = "UPDATE user SET usertype = ?, wannabe = ? WHERE id = (?)";
        $param = array($usertype, false, $userid);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return ($stmt->rowCount() === 1); 
    }

    /** 
     * @function getWannabeTeachers
     * @requires admin rights 
     * @brief getWannabe grabs all wannabe's from the DB
     * @param db: PDOConnection
     * @param usertype: enum['admin', 'teacher', 'student']
     * @return array of wannabeTeachers | null
     */
    static public function getWannabeTeachers($db, $usertype){
        if($usertype !== 'admin') {
            return 0;
        }

        //SQL Injection SAFE query method:
        $query = "SELECT * FROM user WHERE wannabe = (?)";
        $param = array(true);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return $stmt->fetchAll(PDO::FETCH_ASSOC));
    }

    /**
     * @function getUser
     * @param db: PDOConnection
     * @param $email
     * @param $password
     * @return user | null
     */
    static public function getUser($db, $email, $password) {
        //SQL Injection SAFE query method:
        $query = "SELECT * FROM user WHERE email = (?) AND password = (?)";
        $param = array($email, $password);
        $stmt = $db->prepare($query);
        $stmt->execute($param);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}