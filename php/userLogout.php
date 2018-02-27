<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

session_start();
session_destroy();

Urge::gotoHome();

exit;
