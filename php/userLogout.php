<?php
$ROOT = $_SERVER['DOCUMENT_ROOT'];
require_once "$ROOT/classes/Urge.php";

session_start();
session_destroy();


if(isset($_COOKIE['email'])){
  unset($_COOKIE['email']);
  setcookie('email', null, -1, '/');
}

Urge::gotoHome();

exit;
