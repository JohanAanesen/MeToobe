<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$twig = Urge::requireTwig();

if (User::getLoggedInUserid()) {
    Urge::gotoHome();
}

$email = (isset($_COOKIE['email'])) ? $_COOKIE['email'] : "";

echo $twig->render('login.html', array('email' => $email));
