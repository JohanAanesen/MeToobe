<?php

session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/user.php";

$loader = new Twig_Loader_Filesystem("$ROOT/twig");
$twig = new Twig_Environment($loader, array(
    // 'cache' => './cache', /* Only enable cache when everything works correctly */
));

$db = DB::getDBConnection();

$user = new User($db);

$data = [];

$wannabe = array();
$wannabeBool = false;

if ($user->loggedIn()){
    if($user->userData['usertype'] == 'admin'){
        $wannabe = $user->getWannabe();
    }
    if(!empty($wannabe)){
        $wannabeBool = true;
    }


    echo $twig->render('home.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'wannabeUsers' => $wannabe,
        'wannabeBool' => $wannabeBool,
    ));
}else{
    echo $twig->render('home.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
    ));
}

