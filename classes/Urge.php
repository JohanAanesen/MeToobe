<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/classes/Comment.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/Video.php";
require_once "$ROOT/classes/User.php";
require_once "$ROOT/classes/Video.php";


/**
 * @brief Class for removing boilerplate from other code
 */
class Urge {

    public static function gotoHome() {
        header("Location: /");
        exit();
    }

    public static function gotoError($code, $msg) {
        header("Location: /error?code=" . $code . "&msg=" . $msg);
        exit();
    }

    public static function gotoLogin() {
        header("Location: /login");
        exit();
    }

    public static function requireUserid() {
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