<?php

if (!isset($_SESSION['userid'])) {
    header("Location: /login");
    exit();
}