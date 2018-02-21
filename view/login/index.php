<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

$twig = Urge::getTwig();

if (User::isLoggedIn()) {
    Urge::gotoHome();
}

echo $twig->render('login.html');