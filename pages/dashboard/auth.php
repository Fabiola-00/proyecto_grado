<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Si no está logueado, redirigir al login
    // Ajustar la ruta dependiendo de dónde se encuentre el archivo actual
    // Asumimos que el login está en /sistema_sbrab/pages/login.php
    header("Location: /sistema_sbrab/pages/login.php");
    exit;
}

// Headers para prevenir caché y deshabilitar el botón "Atrás"
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
