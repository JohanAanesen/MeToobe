<?php

$ROOT = $_SERVER['DOCUMENT_ROOT'];

require_once "$ROOT/classes/DB.php";
require_once "$ROOT/classes/User.php";

$db = DB::getDBConnection();

if(isset($_POST['yes'])){
    User::updateType($_POST['yes'], "teacher", $db); // "teacher", $db);
}else if(isset($_POST['no'])){
    User::updateType($_POST['no'], "student", $db);
}else if(isset($_POST['admin'])){
    User::updateType($_POST['admin'], "admin", $db);
}

header("Location: /");
