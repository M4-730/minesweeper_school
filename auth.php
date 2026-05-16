<?php
session_start();

if (!isset($_SESSION['activa']) || $_SESSION['activa'] !== true) {
    header("Location: login.html");
    exit();
}
?>