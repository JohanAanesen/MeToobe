<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/php/requirelogin.php";

require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/User.php";


$db = DB::getDBConnection();

$user = new User($db);

$data = [];

if ($user->loggedIn()){
    echo $twig->render('upload.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
    ));
}else{
    echo $twig->render('upload.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
    ));
}