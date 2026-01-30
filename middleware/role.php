<?php
if (!isset($requiredRole) || !is_array($requiredRole)) {
    $requiredRole = [$requiredRole];
}
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], $requiredRole)) {
    echo "Akses ditolak.";
    exit;
}