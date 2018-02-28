<?php
$ROOT        = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$searchQuery = Urge::requireParameter('query');
$db          = Urge::requireDatabase();
$twig        = Urge::requireTwig();
$userid      = User::getLoggedInUserid();

$videoResults = Video::searchVideos($db, $searchQuery);
if (!isset($videoResults)){
    $videoResults = "no";
}

$playlistResults = Playlist::searchPlaylist($db, $searchQuery);
if (!isset($playlistResults)){
    $playlistResults = "no";
}

$user = null;
if ($userid) {
    $user = User::get($db, $userid);
}

echo $twig->render('search.html', array(
    'title' => 'home',
    'userid' => $userid,
    'user' => $user,
    'videoResults' => $videoResults,
    'playlistResults' => $playlistResults,
));