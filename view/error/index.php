<?php
require_once $_SERVER['DOCUMENT_ROOT']."/classes/Urge.php";
$twig = Urge::requireTwig();

echo $twig->render('error.html', array(
    'code' => $_GET['code'],
    'msg' => $_GET['msg']
));
