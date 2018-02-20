<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/DB.php";

class User{

    private $userid = "";
    public $userData = [];
    private $db;
  /*  private $email;
    private $password;
    private $usertype;
    private $wannabe; */


    /**
     * User constructor.
     * @param $db
     */
    function __construct($db){
        $this->db = $db;

        //
        // REGISTER NEW USER
        //
        if(isset($_POST['newemail'])){
            $wannabe = false;
            if(isset($_POST['isTeacher']) && $_POST['isTeacher'] == 'yes'){
                $wannabe = true;
            }
            if(self::checkUniqueUser($_POST['newemail'], $db)){
                //adds new user to DB
                $this->createDBUser($_POST['newemail'], $_POST['newname'], md5($_POST['newpassword']), $wannabe);

                //sets session and stuff..
                $this->findUser($_POST['newemail'], md5($_POST['newpassword']));
            }
        }
        //
        // LOG IN EXISTING USER
        //
        else {
            if (isset($_POST['email'])) {
                $this->findUser($_POST['email'], md5($_POST['password']));
            } else if (isset($_POST['logout'])) {
                unset($_SESSION['userid']);
            } else if (isset($_SESSION['userid'])) {
                $this->userid = $_SESSION['userid'];
                $this->userData['userid'] = $_SESSION['userid'];
                $this->userData['email'] = $_SESSION['email'];
                $this->userData['usertype'] = $_SESSION['usertype'];
                $this->userData['password'] = $_SESSION['password'];
                $this->userData['wannabe'] = $_SESSION['wannabe'];
                $this->userData['fullname'] = $_SESSION['fullname'];

            }
        }
    }

    /** Logged in function, returns true if user is logged in!
     * @return bool
     */
    public function loggedIn(){
        if ($this->userid != ""){
            return true;
        }else{
            return false;
        }
    }

    /**
     * @param $email
     * @param $fullname
     * @param $password
     * @param $wannabe
     */
    public function createDBUser($email, $fullname, $password, $wannabe){
        try {
            $db = $this->db;
            //SQL Injection SAFE query method:
            $query = "INSERT INTO user (id, fullname, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?, ?)";
            $param = array(uniqid(), $fullname, $email, $password, "student", $wannabe);
            $stmt = $db->prepare($query);
            $stmt->execute($param);
        } catch (PDOException $ex) {
            echo "Could not register your funny ass"; //Error message
        }

    }
//DOESNT WORK YET -> needs to be updated :)
    /** Update User in DB
     * @return bool
     */
    public function updateUser(){
        if ($this->failSafe()){
            try {
                $db = $this->db;
                //SQL Injection SAFE query method:
                $query = "UPDATE user SET password = (?), usertype = (?), wannabe = (?) WHERE id = (?)";
                $param = array($this->userData['password'], $this->userData['usertype'], $this->userData['wannabe'], $this->userData['id']);
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

    /**
     * @param $userid
     * @param $usertype
     * @param $db
     * @return bool
     */
    public static function updateType($userid, $usertype, $db){
        try{
            //SQL Injection SAFE query method:
            $query = "UPDATE user SET usertype = ?, wannabe = ? WHERE id = (?)";
            $param = array($usertype, false, $userid);
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
     * @param $email
     * @param $password
     * @return array
     */
    public function findUser($email, $password){
        try {
            $db = $this->db;
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM user WHERE email = (?) AND password = (?)";
            $param = array($email, $password);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->userData = $row;
                $_SESSION['userid'] = $row['id'];
                $_SESSION['fullname'] = $row['fullname'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['usertype'] = $row['usertype'];
                $_SESSION['password'] = $row['password'];
                $_SESSION['wannabe'] = $row['wannabe'];
                $this->userid = $row['id'];
                return array('status'=>'OK');
            }
        } catch (PDOException $ex) {
            return array('status'=>'FAIL', 'errorMessage'=>'Something went wrong');
        }
        return array('status'=>'FAIL', 'errorMessage'=>'Wrong Username/Password!');
    }

    /**
     * @param $db
     * @param $email
     * @return bool
     */
    public static function checkUniqueUser($email, $db){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM user WHERE email = (?)";
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

    /** getWannabe grabs all wannabe's from the DB
     * @return array|null
     */
    public function getWannabe(){
        if($this->userData['usertype'] == 'admin'){
            try{
                $db = $this->db;
                //SQL Injection SAFE query method:
                $query = "SELECT * FROM user WHERE wannabe = (?)";
                $param = array(true);
                $stmt = $db->prepare($query);
                $stmt->execute($param);

                if($stmt->rowCount() > 0) {
                    $users = array();
                    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        $users[] = $row;
                    }
                    return $users;
                }
            }catch(PDOException $ex){
                echo "Something went wrong ".$ex; //Error message
            }
        }
        return null;
    }

    public static function getEmail($db, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT DISTINCT email FROM user WHERE id = (?) LIMIT 1";
            $param = array($userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Can't get user email. Something went wrong!"; //Error message
        }
        return null;
    }

    /**
     * @param $db
     * @param $userid
     * @return null
     */
    public static function getUserStats($db, $userid){
        try{
            //SQL Injection SAFE query method:
            $query = "SELECT fullname, email FROM user WHERE id = (?) LIMIT 1";
            $param = array($userid);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() > 0) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }
        }catch(PDOException $ex){
            echo "Can't get user email. Something went wrong!"; //Error message
        }
        return null;
    }


    /**Fail-safe
     * @return bool
     */
    function failSafe(){
        if (isset($this->userData['userid']) &&
            isset($this->userData['email']) &&
            isset($this->userData['password']) &&
            isset($this->userData['usertype'])){

            return true;
        }else{
            return false;
        }
    }
}
