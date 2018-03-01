<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/classes/Comment.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/User.php";
require_once "$ROOT/classes/Playlist.php";


/**
 * @brief Class for removing boilerplate from other code
 */
class Urge {

    public static function gotoHome() {
        header("Location: /");
        exit();
    }

    public static function gotoError($code, $msg) {
        // @doc http://php.net/manual/en/function.debug-backtrace.php
        header("Location: /error?code=" . $code . "&msg=" . $msg);
        exit();
    }

    public static function gotoLogin() {
        header("Location: /login");
        exit();
    }

    public static function gotoVideo($videoid) {
        header("Location: /video?id=".$videoid);
        exit();
    }

    public static function requireParameter($param) {
        $resultParam = null;
        if (isset($_GET[$param])) {
            $resultParam = $_GET[$param];
        }
        else if (isset($_POST[$param]) ) {
            $resultParam = $_POST[$param];
        }
        else {
            Urge::gotoError(400, "Bad request, missing parameter: " . $param);
        }
        return $resultParam;
    }

    public static function requireParameterArray(...$paramArray) {
        $result = array();
        foreach ($paramArray as $param) {
            array_push($result, Urge::requireParameter($param));
        }
        return $result;
    }

    public static function requireLoggedInUser() {
        $userid = User::getLoggedInUserid();
        if (!$userid) {
            Urge::gotoLogin();
        }
        return $userid;
    }

    public static function requireDatabase() {
        $db = DB::getDBConnection();
        if (!$db) {
            Urge::gotoError(500, "No connection with the database");
        }
        return $db;
    }

    public static function requireTwig() {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];
        $loader = new Twig_Loader_Filesystem("$ROOT/twig");
        return new Twig_Environment($loader, array(
        //    'cache' => './compilation_cache',
        ));
    }

    public static function resetCookies(){
      if(isset($_COOKIE['email'])){
        unset($_COOKIE['email']);
        setcookie('email', null, -1, '/');
      }
    }

    public static function requireFileParameter($fileParam) {
      if ( !isset($_FILES[$fileParam]) ) {
          Urge::gotoError(400, 'Bad request, missing parameter: ' . $fileParam);
      }   
      $tmp_filepath = $_FILES[$fileParam]['tmp_name'];
      $errorCode = $_FILES[$fileParam]['error'];

      if (!is_uploaded_file($tmp_filepath)) {
          $msg = Urge::getFileuploadErrormessage($errorCode);
          Urge::gotoError(400, "Bad request on is_uploaded_file, msg: ". $msg);
      }

      return $_FILES[$fileParam];
    }

    public static function getFileParameterOrNull($fileParam) {
      if ( !isset($_FILES[$fileParam]) ) {
          return null;
      }   
      $tmp_filepath = $_FILES[$fileParam]['tmp_name'];

      if (!is_uploaded_file($tmp_filepath)) {
          return null;
      }

      return $_FILES[$fileParam];
    }

     /**
      * @note stolen from https://stackoverflow.com/a/23173626
      */ 
    public static function scaleThumbnail($_img) {

        $max_width = 400;
        $max_height = 225;
        $img = imagecreatefromstring($_img);

        list($source_image_width, $source_image_height) = getimagesizefromstring($_img);

        $source_gd_image = imagecreatefromstring($_img);

        if ($source_gd_image === false) {
            return false;
        }
        $source_aspect_ratio = $source_image_width / $source_image_height;
        $thumbnail_aspect_ratio = $max_width / $max_height;
        if ($source_image_width <= $max_width && $source_image_height <= $max_height) {
            $thumbnail_image_width = $source_image_width;
            $thumbnail_image_height = $source_image_height;
        } elseif ($thumbnail_aspect_ratio > $source_aspect_ratio) {
            $thumbnail_image_width = (int) ($max_height * $source_aspect_ratio);
            $thumbnail_image_height = $max_height;
        } else {
            $thumbnail_image_width = $max_width;
            $thumbnail_image_height = (int) ($max_width / $source_aspect_ratio);
        }
        $thumbnail_gd_image = imagecreatetruecolor($thumbnail_image_width, $thumbnail_image_height);
        imagecopyresampled($thumbnail_gd_image, $source_gd_image, 0, 0, 0, 0, $thumbnail_image_width, $thumbnail_image_height, $source_image_width, $source_image_height);

        $img_disp = imagecreatetruecolor($max_width, $max_height);
        $backcolor = imagecolorallocate($img_disp,0,0,0);
        imagefill($img_disp,0,0,$backcolor);

        imagecopy($img_disp, $thumbnail_gd_image, (imagesx($img_disp)/2)-(imagesx($thumbnail_gd_image)/2), (imagesy($img_disp)/2)-(imagesy($thumbnail_gd_image)/2), 0, 0, imagesx($thumbnail_gd_image), imagesy($thumbnail_gd_image));


        ob_start();                         // flush/start buffer
        imagepng($img_disp, NULL,9);          // Write image to buffer
        $scaledImage = ob_get_contents();   // Get contents of buffer
        ob_end_clean();                     // Clear buffer

        imagedestroy($source_gd_image);
        imagedestroy($thumbnail_gd_image);
        imagedestroy($img_disp);

        return $scaledImage;
    }

    public static function encodeThumbnailToBase64($thing) {

        if (empty($thing)) {
            return $thing;
        }
        if(isset($thing['thumbnail']))
            $thing['thumbnail'] = base64_encode($thing['thumbnail']);
        return $thing;
    }

    public static function encodeThumbnailsToBase64($thingArray) {

        if (empty($thingArray)) {
            return $thingArray;
        }

        foreach ($thingArray as &$thing) {
            if(isset($thing['thumbnail']))
                $thing['thumbnail'] = base64_encode($thing['thumbnail']);
        }
        return $thingArray;
    }


    // Function for upload errors
    public static function getFileuploadErrormessage($errorNumber){
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
}
