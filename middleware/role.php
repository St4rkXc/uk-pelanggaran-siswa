<?php
if ($_SESSION['role'] !== $requiredRole) {
    echo "403 - Akses ditolak";
    exit;
}
