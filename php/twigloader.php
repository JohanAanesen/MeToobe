<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/vendor/autoload.php";
$loader = new Twig_Loader_Filesystem("$ROOT/twig");
$twig = new Twig_Environment($loader, array(
//    'cache' => './compilation_cache',
));