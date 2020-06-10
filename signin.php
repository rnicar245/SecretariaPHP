<?php
include_once("config/config.php");
include_once("clases/Usuario.php");
include_once("config/funciones.php");

session_start();
if (!isset($_SESSION['mensaje'])){
    $_SESSION['mensaje'] = "";
}

$usuarioTemp = "";
$emailTemp = "";
$nombreTemp = "";



if(isset($_POST['enviar'])){
    $usuarioTemp = limpiarDatos($_POST["username"]);
    $nombreTemp = limpiarDatos($_POST["nombre"]);
    $emailTemp = limpiarDatos($_POST["email"]);
    $usuario = Usuario::getInstancia();

    $anadido = $usuario->set(array("nombre"=> $nombreTemp,
                "email"=> $emailTemp,
                "usuario"=>$usuarioTemp,
                "password"=>limpiarDatos($_POST["pass"]), 
                "estado"=>"bloqueado"));

    $_SESSION['mensaje'] = $usuario->mensaje;
    
    if($anadido){
        header('Location: login.php');
    }
    
    echo $_SESSION['mensaje'];
}

?>
<html>
<head>
    <meta charset="utf-8">
    <title>Biblioteca</title>
</head>
<body>

<?php
    echo "<form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\" enctype=\"multipart/form-data\">";
    echo "<a href=\"cerrar.php\">Volver  </a>";
    echo "<h2>Registro</h2>";
    echo "Nombre:<br> ";
    echo "<input type=\"text\" name=\"nombre\" value=\"".$nombreTemp."\"><br>";
    echo "Usuario:<br> ";
    echo "<input type=\"text\" name=\"username\" value=\"".$usuarioTemp."\"><br>";
    echo "Contraseña:<br> ";
    echo "<input type=\"text\" name=\"pass\"><br>";
    echo "Email:<br> ";
    echo "<input type=\"text\" name=\"email\" value=\"".$emailTemp."\"><br>";
    echo "<br><input type=\"submit\" name=\"enviar\" value=\"Enviar\">";
    echo "</form>";
?>

</body>
</html>