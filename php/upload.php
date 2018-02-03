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
    echo 'ERROR - !is_uploaded_file($tmp_filepath) -  The file was not uploaded using HTTP POST';
    exit();
}

$uid     = $_SESSION['userid'];
$mime    = $_FILES['fileToUpload']['type'];
$name    = $_FILES['fileToUpload']['name'];
$size    = $_FILES['fileToUpload']['size'];
$descr   = $_POST['descr'];

$db = DB::getDBConnection();
$video = new Video($db);

$videoid = $video->add($uid, $name, $descr, $mime, $size); 
if ($videoid === 0) {
    echo 'ERROR - $videoid === 0 - video->add() went wrong.';
    exit();
}

$result = $video->saveToFile($uid, $videoid, $tmp_filepath, $mime);
if ($result === 0) {
    $video->delete($videoid);
    echo 'ERROR - $result = $video->saveToFile() - Unable to move uploaded file to destination folder.';
    exit();
} 

echo "SUCCESS UPLOADING FILE!!";
header("Location: /video?id=$videoid");