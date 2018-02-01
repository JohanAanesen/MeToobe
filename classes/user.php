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

        if(isset($_POST['newemail'])){
            $wannabe = false;
            if(isset($_POST['isTeacher']) && $_POST['isTeacher'] == 'yes'){
                $wannabe = true;
            }
            if(self::checkUniqueUser($_POST['newemail'])){
                //adds new user to DB
                $this->createDBUser($_POST['newemail'], md5($_POST['newpassword']), $wannabe);

                //sets session and stuff..
                $this->findUser($_POST['newemail'], md5($_POST['newpassword']));
            }
        }else{
            if (isset($_POST['email'])) {
                $this->findUser($_POST['email'], md5($_POST['password']));
            } else if (isset($_POST['logout'])) {
                unset($_SESSION['userid']);
            } else if (isset($_SESSION['userid'])) {
                $this->userid = $_SESSION['userid'];
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
     * @param $password
     * @param $wannabe
     */
    public function createDBUser($email, $password, $wannabe){
        try {
            $db = DB::getDBConnection();
            //SQL Injection SAFE query method:
            $query = "INSERT INTO users (userid, email, password, usertype, wannabe) VALUES (?, ?, ?, ?, ?)";
            $param = array(uniqid(), $email, $password, "student", $wannabe);
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
                $db = DB::getDBConnection();
                //SQL Injection SAFE query method:
                $query = "UPDATE users SET password = (?) AND usertype = (?) AND wannabe = (?) WHERE userid = (?)";
                $param = array($this->userData['password'], $this->userData['usertype'], $this->userData['wannabe'], $this->userData['userid']);
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
            $this->userData['usertype'] = "teacher";
            $this->userData['wannabe'] = false;
            $this->updateUser();
        }
    }

    public function isStudent(){
        if ($this->failSafe()){
            $this->userData['usertype'] = "student";
            $this->userData['wannabe'] = false;
            $this->updateUser();
        }
    }

    public function isAdmin(){
        if ($this->failSafe()){
            $this->userData['usertype'] = "admin";
            $this->userData['wannabe'] = false;
            $this->updateUser();
        }
    }


    /**
     * @param $email
     * @param $password
     * @return array
     */
    public function findUser($email, $password){
        try {
            $db = DB::getDBConnection();
            //SQL Injection SAFE query method:
            $query = "SELECT * FROM users WHERE email = (?) AND password = (?)";
            $param = array($email, $password);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

            if($stmt->rowCount() == 1) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $this->userData = $row;
                $_SESSION['userid'] = $row['userid'];
                $this->userid = $row['userid'];
                return array('status'=>'OK');
            }
        } catch (PDOException $ex) {
            return array('status'=>'FAIL', 'errorMessage'=>'Something went wrong');
        }
        return array('status'=>'FAIL', 'errorMessage'=>'Wrong Username/Password!');
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




