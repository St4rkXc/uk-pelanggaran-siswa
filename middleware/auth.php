<?php
if (!isset($_SESSION['login'])) {
    header("Location: " . BASE_URL . "/auth/login.php");
    exit;
}
