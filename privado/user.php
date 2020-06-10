<?php
include_once("../clases/Fichero.php");
include_once("../clases/Clave.php");
include_once("../config/config.php");
include_once("../config/funciones.php");
session_start();

$fichero = Fichero::getInstancia();
$lProcesaFirma = false;
$arrayLetras = array("A", "B", "C", "D", "E", "F", "G", "H");

if(!$_SESSION['logeado'] or $_SESSION['usuario'] == "admin"){
    $_SESSION['mensaje'] = "Nada de trampas.";
    header('Location: ../login.php');
}


if(isset($_POST['delete'])){
    $ficheroABorrar = "";
    $ficheros = $fichero->getFicheros($_SESSION['id']);
    foreach($_POST['delete'] as $clave=>$valor){
        $ficheroABorrar = $clave;
    }
    $fichero->delete($ficheros[$ficheroABorrar]['fichero'], $_SESSION['id']);

    $_SESSION['mensaje'] = $fichero->mensaje;
}

if(isset($_POST['anadirFichero'])){
    move_uploaded_file($_FILES['file']['tmp_name'], "../usuarios/".$_SESSION['usuario']."/".$_FILES['file']['name']);
    $fichero->set(array("idUsuario"=> $_SESSION['id'],
                "descripcion"=>limpiarDatos($_POST['descripcion']),
                "fichero"=>$_FILES['file']['name'],
                "estado"=>"Pendiente"));
    $_SESSION['mensaje'] = $fichero->mensaje;
}

if(isset($_POST['formFirmar'])){
    $ficheros = $fichero->getFicheros($_SESSION['id']);
    foreach($_POST['formFirmar'] as $clave=>$valor){
        $nombreFichero = $clave;
    }

    $_SESSION['nombreFichero'] = $ficheros[$nombreFichero]['fichero'];
    $lProcesaFirma = true;
}

if(isset($_POST['firma'])){
    $arrayLetras = array("A", "B", "C", "D", "E", "F", "G", "H");
    $claseClave = Clave::getInstancia();
    $fichero = Fichero::getInstancia();
    $clave = $claseClave->get(array(
        "idUsuario"=>$_SESSION['id'],
        "fila"=> $arrayLetras[$_SESSION['coordenada'][0]],
        "columna"=>$_SESSION['coordenada'][1]
    ));

    if($clave == limpiarDatos($_POST["clavefirmar"])){
        $fichero->edit($_SESSION['nombreFichero']);
        $_SESSION['mensaje'] = "<span style=\"color:green\">La clave es correcta, documento firmado con éxito.</span>";
    }else{
        $_SESSION['mensaje'] = "<span style=\"color:red\">La clave es incorrecta.</span>";
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
    echo "<nav><ul><li><a href=\"../cerrar.php\">Logout</a></li></ul></nav>";

    if($_SESSION['estado'] != "activo"){
        echo "<p style=\"color:orange\">Advertencia: Su usuario aún no está verificado. Cuando se le verifique recibirá un correo a su dirección de correo electrónico.</p>";
    }else{
        $ficheros = $fichero->getFicheros($_SESSION['id']);

        echo "<form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\" enctype=\"multipart/form-data\">";
        echo "<h2>Añadir fichero</h2><br> ";
        echo "<input type=\"FILE\" name=\"file\" required><br><br>";
        echo "Descripción:<br> ";
        echo "<input type=\"text\" name=\"descripcion\" value=\"\" required><br>";
        echo "<br><input type=\"submit\" name=\"anadirFichero\" value=\"Enviar\">";
        echo "</form>";
        
        echo "</br><form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
        echo"<table border=1>";
        echo "<caption>Ficheros</caption>";
        echo "<tr><td>Nombre del fichero</td><td>Descripcion</td><td>Estado</td></tr>";
        for($i = 0; $i < count($ficheros); $i++){
            echo "<tr>";
            echo "<td>".$ficheros[$i]['fichero']."</td>";
            echo "<td>".$ficheros[$i]['descripcion']."</td>";
            echo "<td>".$ficheros[$i]['estado']."</td>";
            if($ficheros[$i]['estado'] != "Firmado"){
                echo "<td><input type=\"submit\" name=\"formFirmar[".$i."]\" value=\"Firmar\"></td>";
            }else{
                echo "<td><input type=\"submit\" name=\"formFirmar[".$i."]\" value=\"Firmar\" disabled></td>";
            }
            echo "<td><input type=\"submit\" name=\"delete[".$i."]\" value=\"Eliminar fichero\"></td>";  
            echo "</tr>";
        }
        echo "</table>";
        echo "</form>";
    
        if($lProcesaFirma){
            echo "<form action= ".htmlspecialchars($_SERVER["PHP_SELF"])." method= \"POST\">";
            echo "<p><b>Si quiere firmar el documento ".$_SESSION['nombreFichero']." introduzca en el siguiente formulario las claves que han sido enviadas a su correo de las siguientes coordenadas.</b></p>";
            $_SESSION['coordenada'] = array(rand(1,8), rand(1,8));
            echo "<p>Coordenada ".$arrayLetras[--$_SESSION['coordenada'][0]]."".$_SESSION['coordenada'][1]."</p>";
            echo "<input type=\"number\" name=\"clavefirmar\" value=\"\" required><br>";
            echo "<br><input type=\"submit\" name=\"firma\" value=\"Enviar\">";
            echo "</form>";
        }
    }
?>

</body>
</html>