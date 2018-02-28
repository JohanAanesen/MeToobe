<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();

// @note The following 15 lines could be replaced with:
//   list($playlistid, $videoid, $videoRank) = Urge::requireParameterArray('playlist-id','video-id','video-rank');
//                                                                              - JSolsvik 27.02.17
if (!isset($_POST['playlist-id'])) {
    Urge::gotoError(400, "Bad request, missing playlist-id");
}

if (!isset($_POST['video-id'])) {
    Urge::gotoError(400, "Bad request, missing video-id");
}

if (!isset($_POST['video-rank'])) {
    Urge::gotoError(400, "Bad request, missing video-rank");
}

$playlistid = $_POST['playlist-id'];
$videoid    = $_POST['video-id'];
$videoRank  = $_POST['video-rank'];

$newRank = Playlist::updateVideoRanks($db, $playlistid, $videoRank);

if($newRank != null){
    Playlist::removeVideo($db, $playlistid, $videoid, $newRank);
    header('Location: /playlist?id='.$playlistid);
}else{
   // Urge::gotoError(500, "Something went wrong removing the video.");
}



