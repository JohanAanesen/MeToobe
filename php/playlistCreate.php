<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();

$userid = Urge::requireLoggedInUser();

list($playlistTitle, $playlistDesc, $playlistTopic, $playlistCourse) = Urge::requireParameterArray(
    'playlist-title', 
    'playlist-description',
    'playlist-topic',
    'playlist-course'
);
$fileThumbnail = Urge::requireFileParameter('file-thumbnail');

// 1. Validate thumbnail mime type
$mimetypeThumbnail = $fileThumbnail['type'];
if ( $mimetypeThumbnail != 'image/png' &&  $mimetypeThumbnail != 'image/gif' && $mimetypeThumbnail != 'image/jpeg') {
  Urge::gotoError(400, "Bad request, file format has to be [png|gif|jpeg]");
}

// 2. Scale thumbnail to uniform size
$scaledThumbnail = Urge::scaleThumbnail(file_get_contents($fileThumbnail['tmp_name']));

// Add thumbnail
$playlistID = Playlist::create($db, $userid, $playlistTitle, $playlistDesc, $playlistCourse, $playlistTopic, $scaledThumbnail);
    
if(isset($_GET['video-id'])){
   header("Location: /php/playlistAdd.php?playlistid=".$playlistID."&videoid=".$_GET['video-id']);
}else{
    header("Location: /playlist?id=".$playlistID);
}