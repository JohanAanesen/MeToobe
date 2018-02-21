<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$db = Urge::requireDatabase();
$userid = Urge::requireLoggedInUser();


if ( !isset($_FILES['fileToUpload']) ) {
    Urge::gotoError(400, 'Bad request, missing parameter fileToUpload');
}

$tmp_filepath = $_FILES['fileToUpload']['tmp_name'];
$errorCode = $_FILES['fileToUpload']['error'];

if (!is_uploaded_file($tmp_filepath)) {
    $msg = urge_get_fileupload_errormessage($errorCode);
    Urge::gotoError(400, "Bad request on is_uploaded_file, msg: ". $msg);
}

// @note Should do some input validation here, checking that all parameters are checked.
$mime    = $_FILES['fileToUpload']['type'];
$name    = $_FILES['fileToUpload']['name'];
$size    = $_FILES['fileToUpload']['size'];
$descr   = $_POST['descr'];
$title   = $_POST['videotitle'];
$course  = $_POST['videocourse'];
$topic   = $_POST['videotopic'];

// failsafe
if ( $mime != 'video/mp4'  && mime != 'video/webm' && $mime != 'video/ogg') {
    Urge::gotoError(400, "Bad request, file format has to be [mp4|webm|ogg]");
}

$videoid = Video::add($db, $userid, $title, $course, $topic, $descr, $mime, $size);
if (!$videoid) {
    Urge::gotoError(500, 'Server did not manage to upload video');
}

$result = Video::saveToFile($userid, $videoid, $tmp_filepath, $mime);
if (!$result) {
    Video::delete($db, $videoid);
    Urge::gotoError(500,'ERROR - $result = Video::saveToFile() - Unable to move uploaded file to destination folder.');
}

Urge::gotoVideo($videoid);

// Function for upload errors
function urge_get_fileupload_errormessage($errorNumber){
  // Source: http://php.net/manual/en/function.is-uploaded-file.php
  // More source: http://php.net/manual/en/features.file-upload.errors.php
  switch($errorNumber){
    case UPLOAD_ERR_OK: // This should not show up, but is here just in case
      return "There is no error, the file uploaded with success."; break;
    case UPLOAD_ERR_INI_SIZE:
      return "The uploaded file exceeds the upload_max_filesize directive in php.ini."; break;
    case UPLOAD_ERR_FORM_SIZE:
      return "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form."; break;
    case UPLOAD_ERR_PARTIAL:
      return "The uploaded file was only partially uploaded."; break;
    case UPLOAD_ERR_NO_FILE:
      return "No file was uploaded."; break;
    case UPLOAD_ERR_NO_TMP_DIR:
      return "Missing a temporary folder."; break;
    case UPLOAD_ERR_CANT_WRITE:
      return "Failed to write file to disk."; break;
    case UPLOAD_ERR_EXTENSION:
      return "A PHP extension stopped the file upload. PHP does not provide a way to ascertain which extension caused the file upload to stop;
      examining the list of loaded extensions with phpinfo() may help."; break;
    default:
      return "There was a problem with your upload."; break;
    }
}
