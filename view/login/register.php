<?php

$userid = uniqid();
$email = $_POST['email'];
$password = md5($_POST['password']);
$wannabe = false;
$usertype = 'student';


if(isset($_POST['isTeacher']) && $_POST['isTeacher'] == 'yes'){
    $wannabe = true;
}


$db = pdoCon("urgewww");

if(checkUniqueUser($email, $db)) {
    addUser($userid, $email, $password, $usertype, $wannabe, $db);
}

function addUser($userid, $email, $password, $usertype, $wannabe, $db){
    try {
        if ($email != "" && $password != "") {


            //SQL Injection SAFE query method:
            $query = "INSERT INTO users (userid, email, password, brukertype, wannabe) VALUES (?, ?, ?, ?, ?)";
            $param = array($userid, $email, $password, $usertype, $wannabe);
            $stmt = $db->prepare($query);
            $stmt->execute($param);

        }
    } catch (PDOException $ex) {
        echo "Could not register your funny ass"; //Error message
    }
}

function checkUniqueUser($email, $db){
    try{
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

//////////////////////////////PDO CONNECTION//////////////////////////////
function pdoCon($dbname){
    try {
        // Create PDO connection
        $db = new PDO('mysql:host=localhost;dbname='.$dbname.';charset=utf8mb4', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //Sets the $db to the PDO $db :)
    } catch (PDOException $ex) {
        echo "Could not connect to database"; //Error message
    }
    return $db;
}
