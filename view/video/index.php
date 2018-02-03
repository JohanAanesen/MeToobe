<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/user.php";
require_once "$ROOT/classes/Video.php";

$db = DB::getDBConnection();

$user = new User($db);

$data = [];

$video = new Video($db);

$video->viewCountPlus($_GET['id']);

$videoData = $video->findVideo($_GET['id']);



if ($user->loggedIn()){
    echo $twig->render('video.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'yes',
        'user' => $user->userData,
        'videoData' => $videoData,
    ));
}else{
    echo $twig->render('video.html', array(
        'title' => 'home',
        'data' => $data,
        'loggedin' => 'no',
        'videoData' => $videoData,
    ));
}