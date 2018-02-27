<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db                  = Urge::requireDatabase();
$userid              = Urge::requireLoggedInUser();
list($descr, $title) = Urge::requireParameterArray('descr', 'videotitle');
$fileVideo           = Urge::requireFileParameter('file-video');
$fileThumbnail       = Urge::requireFileParameter('file-thumbnail');


// 1. File format validation of video and thumbnail
$tmp_filepath  = $fileVideo['tmp_name'];
$mimetypeVideo = $fileVideo['type'];
if ( $mimetypeVideo != 'video/mp4'  && $mimetypeVideo != 'video/webm' && $mimetypeVideo != 'video/ogg') {
    Urge::gotoError(400, "Bad request, file format has to be [mp4|webm|ogg]");
}

$mimetypeThumbnail = $fileThumbnail['type'];
if ( $mimetypeThumbnail != 'image/png' &&  $mimetypeThumbnail != 'image/gif' && $mimetypeThumbnail != 'image/jpeg') {
  Urge::gotoError(400, "Bad request, file format has to be [png|gif|jpeg]");
}

// 2. Scale thumbnail to uniform size
$thumbnail = file_get_contents($fileThumbnail['tmp_name']);
$scaledThumbnail = Urge::scaleThumbnail($thumbnail);


// 3. Add new video entry to the database4.
$videoid = Video::add($db, $userid, $title, $descr, $mimetypeVideo, $scaledThumbnail);
if (!$videoid) {
    Urge::gotoError(500, 'Server did not manage to upload video');
}

// 4. Save video file to storage
$result = Video::saveToFile($userid, $videoid, $tmp_filepath, $mimetypeVideo);
if (!$result) {
    Video::delete($db, $videoid);
    Urge::gotoError(500,'ERROR - $result = Video::saveToFile() - Unable to move uploaded file to destination folder.');
}

Urge::gotoVideo($videoid);

