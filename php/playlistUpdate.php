<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = Urge::requireLoggedInUser();

$playlistTopic = '';
$playlistCourse = '';

if(isset($_POST['playlist-topic'])){
    $playlistTopic = $_POST['playlist-topic'];
}
if(isset($_POST['playlist-course'])){
    $playlistCourse = $_POST['playlist-course'];
}

list($playlistTitle, $playlistDesc, $playlistID) = Urge::requireParameterArray(
    'playlist-title',
    'playlist-description',
    'playlist-id');

$val = Playlist::update($db, $playlistID, $playlistTitle, $playlistDesc, $playlistCourse, $playlistTopic);

$scaledThumbnail = '';

$fileThumbnail = Urge::getFileParameterOrNull('file-thumbnail');
if ($fileThumbnail) {
    // 1. Validate thumbnail mime type
    $mimetypeThumbnail = $fileThumbnail['type'];
    if ( $mimetypeThumbnail != 'image/png' &&  $mimetypeThumbnail != 'image/gif' && $mimetypeThumbnail != 'image/jpeg') {
        Urge::gotoError(400, "Bad request, file format has to be [png|gif|jpeg]");
    }
    // 2. Scale thumbnail to uniform size
    $scaledThumbnail = Urge::scaleThumbnail(file_get_contents($fileThumbnail['tmp_name']));

    if(!Playlist::uploadThumbnailPlaylist($db, $playlistID, $scaledThumbnail)){
        if($val == 1){
            Urge::gotoError(500, "Something went wrong uploading the thumbnail :/");
        }else{
            Urge::gotoError(400, "Something went wrong updating playlist and thumbnail");
        }

    }
}


if($val == 1){
    header("Location: /playlist?id=".$playlistID);
}

header("Location: /playlist?id=".$playlistID);