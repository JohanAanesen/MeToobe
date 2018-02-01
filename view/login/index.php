<?php
session_start();

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/user.php";

$loader = new Twig_Loader_Filesystem("$ROOT/twig");
$twig = new Twig_Environment($loader, array(
    // 'cache' => './cache', /* Only enable cache when everything works correctly */
));

$db = DB::getDBConnection();

$user = new User($db);

$data = [];

if ($user->loggedIn()) {
    header('Location: /');
}else{
    echo $twig->render('login.html');
}


