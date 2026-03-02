<?php
require_once __DIR__ . '/config/conexion.php';

class Login {

    private $correo;
    private $contrasena;
    private $db;

    public function __construct() {
        $conexion = new Conexion();
        $this->db = $conexion->conectar();
    }

    public function inicializar($correo, $contrasena){
        $this->correo = $correo;
        $this->contrasena = $contrasena;
    }

    public function iniciarSesion(){

        header('Content-Type: application/json');

        $respuesta = [
            'success' => false,
            'message' => 'Correo o contraseña incorrectos'
        ];

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {

            $sql = "SELECT * FROM usuarios WHERE correo = :correo LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':correo', $this->correo);
            $stmt->execute();

            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($usuario && $this->contrasena === $usuario['contrasena']) {

                // Guardar sesión
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['correo'] = $usuario['correo'];
                $_SESSION['rol'] = $usuario['rol'];

                session_write_close();

                echo json_encode([
                    'success' => true,
                    'message' => 'Login exitoso',
                    'rol' => $usuario['rol']
                ]);
                exit;
            }

        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error en el servidor'
            ]);
            exit;
        }

        session_write_close();
        echo json_encode($respuesta);
        exit;
    }

    public function cerrarSesion(){
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        header("Location: index.php");
        exit;
    }
}
?>

