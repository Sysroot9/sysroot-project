<?php
session_start(); // Inicia a sessão
if (isset($_SESSION['response'])) {
    echo $_SESSION['response'];
}
?>