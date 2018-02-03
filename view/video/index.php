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

$videoData = null;


//if videoid is not set, return user to frontpage.
if (isset($_GET['id'])){
    $video->viewCountPlus($_GET['id']);

    $videoData = $video->findVideo($_GET['id']);
}else{
    header("Location: /");
}



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