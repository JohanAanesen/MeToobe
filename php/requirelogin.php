<?php

$logged_in = true;

if ($logged_in == false) {
    header("Location: /view/login");
}