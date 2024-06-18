<?php
$DEBUG_ERRORLEVEL = 0;
if ($DEBUG_ERRORLEVEL == 1) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Inicia a sessão apenas se não estiver iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verifica se o usuário não está logado
if (basename($_SERVER['PHP_SELF']) == 'home.php') {
    if (!isset($_SESSION['userId'])) {
        header("Location: ../auth/login.php");
        exit;
    }
} elseif (basename($_SERVER['PHP_SELF']) == 'login.php') {
    if (isset($_SESSION['userId'])) {
        header("Location: ../client/home.php");
        exit;
    }
} else {
    header("Location: ../login.php");
    exit;
}
?>

