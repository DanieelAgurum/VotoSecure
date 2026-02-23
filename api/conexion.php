<?php
// Configuración de la base de datos
$host = 'localhost';
$dbname = 'VotoSecure';
$username = 'root';
$password = '';

try {
    // Conexión usando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Configurar atributos PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En caso de error, mostrar mensaje (en producción, registrar en log)
    die("Error de conexión: " . $e->getMessage());
}

