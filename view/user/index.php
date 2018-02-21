<?php
$ROOT       = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();
$twig   = Urge::requireTwig();

$user          = User::get($db, $userid);
//$userStats     = User::getUserStats($db, $userid);
$userVideos    = Video::getUsersVideos($db, $userid);
$userPlaylists = Playlist::getUserPlaylist($db, $userid);

echo $twig->render('user.html', array(
    'title' => 'home',
    'userid' => $userid,
    'user' => $user,
    'userVideos' => $userVideos,
    'userStats' => null,
    'userPlaylists' => $userPlaylists,
));