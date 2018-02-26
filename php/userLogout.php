<?php

session_start();
session_destroy();

header('Location: /');

if(isset($_COOKIE['email'])){
  unset($_COOKIE['email']);
  setcookie('email', null, -1, '/');
}

exit;
