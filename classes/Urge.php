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
}