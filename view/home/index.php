<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db     = Urge::requireDatabase();
$twig   = Urge::requireTwig();
$userid = User::getLoggedInUserid();

$user = null;
if ($userid) {
    $user = User::get($db, $userid);
    if (!isset($user))
        Urge::gotoError(500,'Server encountered an error. It should be possible to get user information from logged in user.');
}

$subscribedVideos = null;
$subscribedPlaylists = null;
if($userid){
    $subscribedVideos = Video::getSubscribedVideos($db, $userid);
    $subscribedPlaylists = Playlist::getUserPlaylist($db, $userid);
}
$newVideos = Video::getNewVideos($db);
$newPlaylists = Playlist::getNewPlaylists($db);

if (!empty($newVideos))
    $newVideos = Urge::encodeThumbnailsToBase64($newVideos);

if (!empty($subscribedVideos))
    $subscribedVideos = Urge::encodeThumbnailsToBase64($subscribedVideos);

if (!empty($subscribedPlaylists))
    $subscribedPlaylists = Urge::encodeThumbnailsToBase64($subscribedPlaylists);

echo $twig->render('home.html', array(
    'title' => 'home',
    'userid' => $userid,
    'user' => $user,
    'wannabeUsers' => User::getWannabeTeachers($db),
    'admin' => User::isAdmin(),
    'newVideos' => $newVideos,
    'newPlaylists' => $newPlaylists,
    'subscribedVideos' => $subscribedVideos,
    'subscribedPlaylists' => $subscribedPlaylists,
));


