<?php
require_once "../classes/DB.php";

class User{

    protected $userid;
    protected $email;
    protected $password;
    protected $usertype;
    protected $wannabe;


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
        if (isset($this->userid) && isset($this->email) && isset($this->password) && isset($this->usertype)){
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

}




