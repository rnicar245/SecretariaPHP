<?php
    require_once('DBAbstractModel.php');

    class Clave extends DBAbstractModel{
        private static $instancia;

        private $id;
        private $idUsuario;
        private $fila;
        private $columna;
        private $valor;

        public static function getInstancia() {
            if (!isset(self::$instancia)) {
                $miclase = __CLASS__;
                self::$instancia = new $miclase;
            }
            return self::$instancia;
        }

        public function __clone() {
            trigger_error('La clonación no es permitida.', E_USER_ERROR);
        }

        public function set($user_data=array()) {
                foreach ($user_data as $campo=>$valor) {
                    $$campo = $valor;
                }
                $this->query = "INSERT INTO clavefirma (idUsuario, fila, columna, valor) VALUES (:idUsuario, :fila, :columna, :valor)";
                $this->parametros['idUsuario']= $idUsuario;
                $this->parametros['fila']= $fila;
                $this->parametros['columna']= $columna;
                $this->parametros['valor']= $valor;
                $this->get_results_from_query();
                //$this->execute_single_query();
                $this->mensaje = "<span style=\"color:green\">Clave agregada exitosamente</span>";
                return true; 
        }
        public function guardarenDB() {
            $this->query = "INSERT INTO libro (id, titulo, autor) VALUES (:id, :titulo, :autor)";
            $this->parametros['id']= $this->id;
            $this->parametros['titulo']= $this->titulo;
            $this->parametros['autor']= $this->autor;
            $this->get_results_from_query();
            $this->mensaje = 'Usuario agregado exitosamente';
        }
        // public function getLibros($datos){
        //     $this->query = "SELECT id, titulo, autor FROM libro WHERE titulo like :filtro OR autor like : filtro";
        //     $this->parametros["id"] = $id;
        //     $this->get_results_from_query();
        // }  
        public function get($user_data=array()){
                foreach ($user_data as $campo=>$valor) {
                    $$campo = $valor;
                }
                $this->query = "SELECT valor FROM clavefirma WHERE fila=:fila AND columna=:columna AND idUsuario=:idUsuario";
                $this->parametros["fila"] = $fila;
                $this->parametros["columna"] = $columna;
                $this->parametros["idUsuario"] = $idUsuario;
                $this->get_results_from_query();
            if(count($this->rows) >= 1){
                foreach($this->rows[0] as $propiedad=>$valor){
                    $this->propiedad = $valor;
                }$this->mensaje="<span style=\"color:green\">Clave encontrada</span>";
                return $valor;
            }else{
                $this->mensaje="<span style=\"color:red\">Clave no encontrada</span>";
            }
            return true;
        }

        public function getUsuarioCorrecto($usuario="", $password =""){
            if($usuario!=""){
                $this->query = "SELECT usuario FROM usuario WHERE usuario=:usuario AND password=:password";
                $this->parametros["usuario"] = $usuario;
                $this->parametros["password"] = $password;
                $this->get_results_from_query();
            }
            if(count($this->rows) == 1){
                foreach($this->rows[0] as $propiedad=>$valor){
                    $this->propiedad = $valor;
                }$this->mensaje="<span style=\"color:green\">Logeado correctamente</span>";
                return true;
            }else{
                $this->mensaje="<span style=\"color:red\">Usuario no encontrado</span>";
            }
            return false;
        }

        public function getEstado($usuario=""){
            if($usuario!=""){
                $this->query = "SELECT estado FROM usuario WHERE usuario=:usuario";
                $this->parametros["usuario"] = $usuario;
                $this->get_results_from_query();
            }
            if(count($this->rows) == 1){
                foreach($this->rows[0] as $propiedad=>$valor){
                    $this->mensaje="<span style=\"color:green\">Usuario encontrado</span>";
                    $this->propiedad = $valor;
                    return $valor;
                }
            }else{
                $this->mensaje="<span style=\"color:red\">Usuario no encontrado</span>";
                return false;
            }
            return "invitado";
        }

        public function getFicheros($idUsuario = ""){
            $this->query = "SELECT * FROM documentos WHERE idUsuario=:idUsuario";
            $this->parametros['idUsuario']=$idUsuario;
            $this->get_results_from_query();
            return $this->rows;
        }

        public function editEstado($usuario="") {
            $nuevoEstado = $this->getEstado($usuario);
            if($nuevoEstado != false){
                if($nuevoEstado == "bloqueado"){
                    $nuevoEstado = "activo";
                }else{
                    $nuevoEstado = "bloqueado";
                }     
                $this->query = "UPDATE usuario SET estado=:nuevoEstado WHERE usuario = :usuario ";
                $this->parametros['usuario']=$usuario;
                $this->parametros['nuevoEstado']=$nuevoEstado;
                
                $this->get_results_from_query();
                $this->mensaje = "<span style=\"color:green\">Estado modificado con éxito</span>";
            }
        }

        public function edit($fichero = "") {
            $this->query = "UPDATE documentos SET estado=\"Firmado\" WHERE fichero = :fichero";
            $this->parametros['fichero']=$fichero;
            
            $this->get_results_from_query();
            $this->mensaje = "<span style=\"color:green\">Fichero firmado con éxito</span>";
        }
        
        public function delete($fichero="") {
            $this->query = "DELETE FROM documentos WHERE fichero = :fichero";
            $this->parametros['fichero']=$fichero;
            $this->get_results_from_query();
            $this->mensaje = "<span style=\"color:green\">Fichero eliminado con éxito</span>";
        }

        public function persist(){

        }
        public function getMensaje(){
            return $this->mensaje;
        }
    }
?>