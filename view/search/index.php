<?php
$ROOT        = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$searchQuery = Urge::requireParameter('query');
$db          = Urge::requireDatabase();
$twig        = Urge::requireTwig();
$userid      = User::getLoggedInUserid();

$videoResults = Video::searchVideos($db, $searchQuery);
$videoResults = Urge::encodeThumbnailsToBase64($videoResults);

$playlistResults = Playlist::searchPlaylist($db, $searchQuery);
$playlistResults = Urge::encodeThumbnailsToBase64($playlistResults);


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