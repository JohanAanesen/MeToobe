<?php
require_once "../classes/user.php";

$userid = uniqid();
$email = $_POST['email'];
$password = md5($_POST['password']);
$wannabe = false;
$usertype = 'student';


if(isset($_POST['isTeacher']) && $_POST['isTeacher'] == 'yes'){
    $wannabe = true;
}

if(User::checkUniqueUser($email)) {
    $user = new User($userid, $email, $password, $usertype, $wannabe);
    if ($user->createDBUser()){
        echo "Success!";
    }else{
        echo "Something went wrong, try again!";
    }
}
