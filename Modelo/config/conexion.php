<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "votosecure";

$conexion = new mysqli($host, $user, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
class Conexion {

    private $host = "localhost";
    private $db = "votosecure";
    private $user = "root";
    private $pass = "";
    private $charset = "utf8mb4";

    public function conectar() {

        $dsn = "mysql:host=$this->host;dbname=$this->db;charset=$this->charset";

        try {
            $pdo = new PDO($dsn, $this->user, $this->pass);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        } catch (PDOException $e) {
            die("Error de conexión");
        }
    }

}