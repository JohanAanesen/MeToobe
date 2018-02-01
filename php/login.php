<?php

require_once "../classes/user.php";

$email = $_POST['email'];
$password = md5($_POST['password']);


$user = User::findUser($email, $password);

if (isset($user)){
    echo "Success!!";
}else{
    echo "Username or PW is wrong!";
}