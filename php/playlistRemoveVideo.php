<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$userid = Urge::requireLoggedInUser();
$db     = Urge::requireDatabase();

list($playlistid, $videoid, $videoRank) = Urge::requireParameterArray('playlist-id','video-id','video-rank');

echo $videoRank.": ";

$newRank = Playlist::updateVideoRanks($db, $playlistid, $videoid, $videoRank);

if($newRank != null){
 //   echo "   pl".$playlistid."   vi".$videoid."    r".$newRank."  ";
    Playlist::removeVideo($db, $playlistid, $videoid, $newRank);
   // header('Location: /playlist?id='.$playlistid);
}else{
    Urge::gotoError(500, "Something went wrong removing the video.");
}



