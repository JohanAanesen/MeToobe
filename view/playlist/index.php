<?php
session_start();
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/vendor/autoload.php";
require_once "$ROOT/php/twigloader.php";

echo $twig->render('playlist.html');