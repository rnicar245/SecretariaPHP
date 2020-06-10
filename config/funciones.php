<?php
function limpiarDatos($dato){
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

function mostrarMenu($rol){
    switch($rol){
        case "admin":
            echo "<a href=\"cerrar.php\">Salir </a><a href=\"modificarUsuarios.php\">Usuarios  </a><a href=\"modificarLibros.php.php\">Libros</a>";
        break;
        case "usuario":
            echo "<a href=\"cerrar.php\">Salir </a>";
        break;
        case "lector":
            echo "<a href=\"cerrar.php\">Salir </a><a href=\"cerrar.php\">Salir </a>";
        break;
            
    }
}
?>