<?php
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once(__DIR__ . '/../config/database.php');
?>