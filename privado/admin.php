<?php
require "../phpmailer/class.phpmailer.php";
include_once("../clases/Usuario.php");
include_once("../clases/Clave.php");
include_once("../config/config.php");
include_once("../config/funciones.php");
session_start();

$usuario = Usuario::getInstancia();


if(!$_SESSION['logeado'] or $_SESSION['usuario'] != "admin"){
    $_SESSION['mensaje'] = "Nada de trampas.";
    header('Location: ../login.php');
}

if(isset($_POST['cambiarEstado'])){
    $usuarios = $usuario->getUsuarios("%".$_SESSION['busqueda']."%");
    $indice = "";
    foreach($_POST['cambiarEstado'] as $i=>$valor){
        $indice = $i;
    }
    $usuario->editEstado($usuarios[$indice]['usuario']);
    $_SESSION['mensaje'] = $usuario->mensaje;
    if($usuario->getEstado($usuarios[$indice]['usuario']) == "activo"){
        $nombreFichero = $usuarios[$indice]['usuario'];
            if(!file_exists("../usuarios/".$nombreFichero)){
                mkdir("../usuarios/".$nombreFichero, 0777);
                crearClaves($nombreFichero);
                enviarClaves("../usuarios/".$nombreFichero."/claves".$nombreFichero.".txt", $usuarios[$indice]['email']);
            }
    }
}

if(isset($_POST['delete'])){
    $usuarioABorrar = "";
    $usuarios = $usuario->getUsuarios("%".$_SESSION['busqueda']."%");
    foreach($_POST['delete'] as $clave=>$valor){
        $usuarioABorrar = $clave;
    }
    $usuario->delete($usuarios[$usuarioABorrar]['usuario']);
    borrarDirectorio("../usuarios/".$usuarios[$usuarioABorrar]['usuario']);

    $_SESSION['mensaje'] = $usuario->mensaje;
}

if(isset($_POST['buscar'])){
    $_SESSION['busqueda'] = limpiarDatos($_POST['busqueda']);
}

function enviarClaves($ruta, $email){
    $mail = new PHPMailer();

    $mail->CharSet = "utf-8";
    $mail->From = "arceuse1999php@gmail.com";
    $mail->FromName = "Secretaría Virtual";
    $mail->Subject = "Claves para firma virtual";

    $mail->addAddress($email, "");
    $mail->msgHTML("En este correo tiene adjuntas sus claves para realizar sus firmas virtuales.\nEste es un mensaje automatizado, por favor, no responda a este correo.");
    $mail->addAttachment($ruta);

    $mail->send();
}

function borrarDirectorio($ruta) {
	$archivos = glob($ruta . '/*');
	foreach ($archivos as $archivo) {
		is_dir($archivo) ? removeDirectory($archivo) : unlink($archivo);
	}
	rmdir($ruta);
	return;
}

function crearClaves($usuario){
    $claseClave = Clave::getInstancia();
    $claseUsuario = Usuario::getInstancia();
    if(!file_exists("../usuarios/".$usuario."/claves".$usuario.".txt")){
        $file = fopen("../usuarios/".$usuario."/claves".$usuario.".txt", "w") or exit("No se ha podido crear el archivo.");
        
        $contador = 0;
        $arrayLetras = array("A", "B", "C", "D", "E", "F", "G", "H");
        $nuevoTexto = "       1       2       3       4       5       6       7       8\n";
        for($i = 0; $i < 8; $i++){
            $nuevoTexto = $nuevoTexto."".$arrayLetras[$i]."     ";
            for($j=0; $j< 8; $j++){
                $numeroAleatorio = rand(0, 9)."".rand(0, 9)."".rand(0, 9);

                $claseClave->set(array(
                    "idUsuario"=> $claseUsuario->getId($usuario),
                    "fila"=>$arrayLetras[$i],
                    "columna"=>$j+1,
                    "valor"=>$numeroAleatorio
                ));

                $nuevoTexto = $nuevoTexto."".$numeroAleatorio."     ";
            }
            $nuevoTexto = $nuevoTexto."\n";
        }
        file_put_contents("../usuarios/".$usuario."/claves".$usuario.".txt", $nuevoTexto);
        fclose($file);
    }
    
}


?>
<html>
<head>
    <meta charset="utf-8">
    <title>Biblioteca</title>
</head>
<body>

<?php
    echo "<p>".$_SESSION['mensaje']."</p>";
    echo "<br>Usted está logeado como ".$_SESSION['usuario'].".<br>";
    echo "<nav><ul><li><a href=\"../cerrar.php\">Logout</a></li><li><a href=\"claves.php\">Claves de usuario</a></li></ul></nav>";

    echo "</br><form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
    echo "<br>Búsqueda:";
    echo "<input type=\"text\" name=\"busqueda\" value=\"".$_SESSION['busqueda']."\">";
    echo "<input type=\"submit\" name=\"buscar\" value=\"Buscar\">";
    $usuarios = $usuario->getUsuarios("%".$_SESSION['busqueda']."%");

    echo "</form>";

    echo "</br><form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
    echo"<table border=1>";
    echo "<caption>Usuarios</caption>";
    echo "<tr><td>Nombre de usuario</td><td>Estado</td></tr>";
    for($i = 0; $i < count($usuarios); $i++){
        echo "<tr>";
        $adminEncontrado = false;
        foreach($usuarios[$i] as $iden=>$datos){
            if($iden == "usuario"){
                if($datos == "admin"){
                    $adminEncontrado = true;
                    break;       
                }
            }
        }
        if(!$adminEncontrado){
            echo "<td>".$usuarios[$i]['usuario']."</td>";
            echo "<td>".$usuarios[$i]['estado']."</td>";
            echo "<td><input type=\"submit\" name=\"cambiarEstado[".$i."]\" value=\"Cambiar estado\"></td>";
            echo "<td><input type=\"submit\" name=\"delete[".$i."]\" value=\"Eliminar usuario\"></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</form>";

?>

</body>
</html>