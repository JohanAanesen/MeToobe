<?php
require_once "../classes/DB.php";

class User{

    private $userid;
    private $email;
    private $password;
    private $usertype;
    private $wannabe;


    /**
     * User constructor.
     * @param string $userid User id
     * @param string $email User email
     * @param string $password User password
     * @param string $usertype User type
     * @param bool $wannabe User wannabe
     */
    function __construct($userid = "", $email, $password, $usertype = 'student', $wannabe = false)
    {
        $this->userid = $userid;
        $this->email = $email;
        $this->password = $password;
        $this->usertype = $usertype;
        $this->wannabe = $wannabe;
    }

    /**
     * @return bool
     */
    public function createDBUser(){
        if ($this->failSafe()){
            try {
                $db = DB::getDBConnection();
                //SQL Injection SAFE query method:
                $query = "INSERT INTO users (userid, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?)";
                $param = array($this->userid, $this->email, $this->password, $this->usertype, $this->wannabe);
                $stmt = $db->prepare($query);
                $stmt->execute($param);
            } catch (PDOException $ex) {
                echo "Could not register your funny ass"; //Error message
                return false;
            }
            return true;
        }
        return false;
    }

    /** Update User in DB
     * @return bool
     */
    public function updateUser(){
        if ($this->failSafe()){
            try {
                $db = DB::getDBConnection();
                //SQL Injection SAFE query method:
                $query = "UPDATE users SET password = (?) AND usertype = (?) AND wannabe = (?) WHERE userid = (?)";
                $param = array($this->password, $this->usertype, $this->wannabe, $this->userid);
                $stmt = $db->prepare($query);
                $stmt->execute($param);
            } catch (PDOException $ex) {
                echo "Could not update password"; //Error message
                return false;
            }
            return true;
        }
        return false;
    }


    public function isTeacher(){
        if ($this->failSafe()){
            $this->usertype = "teacher";
            $this->wannabe = false;
            $this->updateUser();
        }
    }

    public function isStudent(){
        if ($this->failSafe()){
            $this->usertype = "student";
            $this->wannabe = false;
            $this->updateUser();
        }
    }

    public function isAdmin(){
        if ($this->failSafe()){
            $this->usertype = "admin";
            $this->wannabe = false;
            $this->updateUser();
        }
    }


    /**
     * @param $email
     * @param $password
     * @return User
     */
    public static function findUser($email, $password)
    {
        try {
            $db = DB::getDBConnection();
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM users WHERE email = (?) AND password = (?)";
            $param = array($email, $password);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $user = new User($row['userid'],$row['email'],$row['password'],$row['usertype'],$row['wannabe']);
                return $user;
            }
        } catch (PDOException $ex) {
            echo "Could not find user"; //Error message
        }
        return null;
    }

    /**
     * @param $email
     * @return bool
     */
    public static function checkUniqueUser($email){
        try{
            $db = DB::getDBConnection();
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM users WHERE email = (?)";
            $param = array($email);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() > 0) {
                echo "Email already registered!";
                return false;
            }else return true;
        }catch(PDOException $ex){
            echo "Email already registered".$ex; //Error message
        }
        return true;
    }

    /**Fail-safe
     * @return bool
     */
    function failSafe(){
        if (isset($this->userid) && isset($this->email) && isset($this->password) && isset($this->usertype)){
            return true;
        }else{
            return false;
        }
    }
}




