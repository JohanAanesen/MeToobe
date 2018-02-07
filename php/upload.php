<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/php/requirelogin.php";

require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/DB.php";


if ( !isset($_FILES['fileToUpload']) ) {
    echo 'ERROR - !isset($_FILES[\'fileToUpload\']) - fileToUpload is not set';
    exit();
}

$tmp_filepath = $_FILES['fileToUpload']['tmp_name'];

if (!is_uploaded_file($tmp_filepath)) {
    echo 'ERROR - !is_uploaded_file($tmp_filepath)';
    exit();
}

$uid     = $_SESSION['userid'];
$mime    = $_FILES['fileToUpload']['type'];
$name    = $_FILES['fileToUpload']['name'];
$size    = $_FILES['fileToUpload']['size'];
$descr   = $_POST['descr'];
$title   = $_POST['videotitle'];

$db = DB::getDBConnection();

// failsafe
if ($mime == 'video/mp4' || $mime == 'video/webm' || $mime == 'video/ogg'){
    $videoid = Video::add($db, $uid, $title, $descr, $mime, $size);
    if ($videoid === 0) {
        echo 'ERROR - $videoid === 0 - Video::add() went wrong.';
        exit();
    }

    $result = Video::saveToFile($uid, $videoid, $tmp_filepath, $mime);
    if ($result === 0) {
        Video::delete($db, $videoid);
        echo 'ERROR - $result = Video::saveToFile() - Unable to move uploaded file to destination folder.';
        exit();
    }

    echo "SUCCESS UPLOADING FILE!!";
    header("Location: /video?id=$videoid");
}else{
    echo "Not a valid file.";
}