<?php 

session_start();
if (isset($_POST['logout'])) {
    unset($_SESSION['authenticated']);
    header("Location: index.php");
}