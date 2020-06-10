<?php
include_once("../clases/Usuario.php");
include_once("../config/config.php");
include_once("../config/funciones.php");
session_start();

$usuario = Usuario::getInstancia();


if(!$_SESSION['logeado'] or $_SESSION['usuario'] != "admin"){
    $_SESSION['mensaje'] = "Nada de trampas.";
    header('Location: ../login.php');
}

if(isset($_POST['buscar'])){
    $_SESSION['busqueda'] = limpiarDatos($_POST['busqueda']);
}

if(isset($_POST['descarga'])){
    $indice = "";
    $usuarios = $usuario->getUsuarios("%".$_SESSION['busqueda']."%");
    foreach($_POST['descarga'] as $clave=>$valor){
        $indice = $clave;
    }
   
    $fileName = "claves".$usuarios[$indice]['usuario'].".txt";
    $filePath = "../usuarios/".$usuarios[$indice]['usuario']."/".$fileName;
    if(!empty($fileName) && file_exists($filePath)){
        header("Content-disposition: attachment; filename=$fileName");
        header("Content-type: text/plain");

        $_SESSION['mensaje'] = "<span style=\"color:green\">Descarga realizada con éxito.</span>";

        readfile($filePath);
        exit;
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
    echo "<nav><ul><li><a href=\"../cerrar.php\">Logout</a></li><li><a href=\"admin.php\">Gestionar usuarios</a></li></ul></nav>";

    echo "</br><form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
    echo "<br>Búsqueda:";
    echo "<input type=\"text\" name=\"busqueda\" value=\"".$_SESSION['busqueda']."\">";
    echo "<input type=\"submit\" name=\"buscar\" value=\"Buscar\">";
    $usuarios = $usuario->getUsuarios("%".$_SESSION['busqueda']."%");

    echo "</form>";

    echo "</br><form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
    echo"<table border=1>";
    echo "<caption>Usuarios</caption>";
    echo "<tr><td>Nombre de usuario</td><td>Claves</td></tr>";
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
            echo "<td><input type=\"submit\" name=\"descarga[".$i."]\" value=\"Descargar claves\"></td>";
        }
        echo "</tr>";
    }
    echo "</table>";
    echo "</form>";

?>

</body>
</html>