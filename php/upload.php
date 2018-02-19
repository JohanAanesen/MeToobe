<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/php/twigloader.php";
require_once "$ROOT/php/requirelogin.php";

require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/DB.php";

// Function for upload errors
function urge_print_upload_error($errorNumber){
  // Source: http://php.net/manual/en/function.is-uploaded-file.php
  // More source: http://php.net/manual/en/features.file-upload.errors.php
  switch($errorNumber){
    case UPLOAD_ERR_OK: // This should not show up, but is here just in case
      echo "There is no error, the file uploaded with success."; break;
    case UPLOAD_ERR_INI_SIZE:
      echo "The uploaded file exceeds the upload_max_filesize directive in php.ini."; exit(); break;
    case UPLOAD_ERR_FORM_SIZE:
      echo "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."; exit(); break;
    case UPLOAD_ERR_PARTIAL:
      echo "The uploaded file was only partially uploaded."; exit(); break;
    case UPLOAD_ERR_NO_FILE:
      echo "No file was uploaded."; exit(); break;
    case UPLOAD_ERR_NO_TMP_DIR:
      echo "Missing a temporary folder."; exit(); break;
    case UPLOAD_ERR_CANT_WRITE:
      echo "Failed to write file to disk."; exit(); break;
    case UPLOAD_ERR_EXTENSION:
      echo "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop;
      examining the list of loaded extensions with phpinfo() may help."; exit(); break;
    default:
      echo "There was a problem with your upload."; exit (); break;
    }
}

if ( !isset($_FILES['fileToUpload']) ) {
    echo 'ERROR - !isset($_FILES[\'fileToUpload\']) - fileToUpload is not set';
    exit();
}

$tmp_filepath = $_FILES['fileToUpload']['tmp_name'];
$errorCode = $_FILES['fileToUpload']['error'];

if (!is_uploaded_file($tmp_filepath)) {
   urge_print_upload_error($errorCode);
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
