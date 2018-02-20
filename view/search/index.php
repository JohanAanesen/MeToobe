<?php

session_start();

$ROOT        = $_SERVER['DOCUMENT_ROOT'];
$searchQuery = $_GET['query'];

//failsafe, no search result if no query
if (!isset($searchQuery)){
    header("Location: /");
    exit();
}

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/User.php";
require_once "$ROOT/classes/Video.php";

$db = DB::getDBConnection();
$user = new User($db);
$userid = "";
if($user->loggedIn()) {
    $userid = $_SESSION['userid'];
}
$data = [];
$searchResults = Video::searchVideos($db, $searchQuery);
if (!isset($searchResults)){
    $searchResults = "no";
}

if ($user->loggedIn()){
    echo $twig->render('search.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'searchResults' => $searchResults,
    ));
}else{
    echo $twig->render('search.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'searchResults' => $searchResults,
    ));
}