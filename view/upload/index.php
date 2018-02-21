<?php
$ROOT    = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

Urge::requireLoggedInUser();
$twig = Urge::requireTwig();

echo $twig->render('upload.html', array(
    'title' => 'home',
    'loggedin' => 'yes',
));
