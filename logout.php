<?php
// Simple test version
session_start();
session_destroy();
header("Location: index.php");
exit;
?>