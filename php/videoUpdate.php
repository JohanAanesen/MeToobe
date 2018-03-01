<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db      = Urge::requireDatabase();
$userid  = User::getLoggedInUserid();

list($videoID, $videoTitle, $videoDesc, $videoOwner) = Urge::requireParameterArray(
    'video-id','video-title', 'video-description', 'video-owner');

if ($userid == $videoOwner){
    if(Video::updateVideoTitleDescription($db, $videoID, $videoTitle, $videoDesc)){
        Urge::gotoVideo($videoID);
    }
}else{
    Urge::gotoError(400, "Something went wrong updating the video.");
}