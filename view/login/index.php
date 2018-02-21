<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$twig = Urge::requireTwig();

if (User::getLoggedInUserid()) {
    Urge::gotoHome();
}

echo $twig->render('login.html');