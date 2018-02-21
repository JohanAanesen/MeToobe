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

    public static function get_Userid_Database_Twig() {
        
        return array(
            User::isLoggedIn(),
            Urge::requireDatabase(),
            Urge::getTwig()
        );
    }

    public static function require_Userid_Database_Twig() {
        
        return array(
            Urge::requireUserid(),
            Urge::requireDatabase(),
            Urge::getTwig()
        );
    }

    public static function require_Userid_Database() {
        
        return array(
            Urge::requireUserid(),
            Urge::requireDatabase(),
        );
    }

    public static function require_Userid() {
        
        return array(
            Urge::requireUserid(),
        );
    }

    public static function requireUserid() {
        
        return User::isLoggedInOrRedirectTo("/login");        
    }
    
    public static function requireDatabase() {
        
        return DB::getDBConnection();
    }

    public static function getTwig() {
        $ROOT = $_SERVER['DOCUMENT_ROOT'];        
        $loader = new Twig_Loader_Filesystem("$ROOT/twig");
        return new Twig_Environment($loader, array(
        //    'cache' => './compilation_cache',
        )); 
    }
}