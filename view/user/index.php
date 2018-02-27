<?php
$ROOT       = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = User::getLoggedInUserid();

$loggedIn = isset($userid);
$owner = false;

$db     = Urge::requireDatabase();
$twig   = Urge::requireTwig();

$userPageId = Urge::requireParameter('id');

$user          = User::get($db, $userid);
//$userStats     = User::getUserStats($db, $userPageId);
$userStats     = User::get($db, $userPageId);
$userVideos    = Video::getUsersVideos($db, $userPageId);
$userPlaylists = Playlist::getUserPlaylist($db, $userPageId);

if($userStats['id'] == $userid){
    $owner = true;
}

echo $twig->render('user.html', array(
    'title' => 'home',
    'userid' => $userid,
    'loggedIn' => $loggedIn,
    'owner' => $owner,
    'user' => $user,
    'userVideos' => $userVideos,
    'userStats' => $userStats,
    'userPlaylists' => $userPlaylists,
));