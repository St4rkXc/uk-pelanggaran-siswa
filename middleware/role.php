<?php
if (!isset($requiredRole) || !is_array($requiredRole)) {
    $requiredRole = [$requiredRole];
}

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    header("Location: " . BASE_URL . "/pages/403/index.php");
    exit;
}