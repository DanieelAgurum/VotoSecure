<?php 

require_once __DIR__ . '/../Modelo/login.php'; 

class LoginCtrl { private $login; public function __construct(){

$this->login = new Login(); 
 } public function iniciarSesion($correo, $contrasena){
$this->login->inicializar($correo, $contrasena); $this->login->iniciarSesion();}

public function cerrarSesion() { $this->login->cerrarSesion(); 
} 
     
} 
?>