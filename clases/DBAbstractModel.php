<?php
/*
define("DBHOST", 'localhost');
define("DBNAME", 'bookmarks');
define("DBUSER", 'root');
define("DBPASS", 'root');
define("DBPORT", '3306');
*/

    abstract class DBAbstractModel {
        private static $db_host = DBHOST;
        private static $db_user = DBUSER;
        private static $db_pass = DBPASS;
        private static $db_name = DBNAME;
        private static $db_port = DBPORT;

        protected $conn; //Manejador de la BD

        //Manejo de consultas:
        protected $query;
        protected $parametros = array();
        protected $rows = array();

        public $mensaje = 'Hecho';

        //Métodos abstractos:
        abstract protected function get();
        abstract protected function set();
        abstract protected function edit();
        abstract protected function delete();

        //Crear conexión a la base de datos:
        protected function open_connection() {
            try {

                $this->conn = new PDO('mysql:host='.self::$db_host.';dbname='.self::$db_name,self::$db_user, self::$db_pass);
        
                $this->conn->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $this->conn->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, "SET NAMES utf8");
        
                return $this->conn;
            } catch (PDOException $e) {
                print $e;
            }
        }

        //Desconectar la base de datos:
        protected function close_connection() {
            $this->conn = null;
        }

        public function lastInsert(){
            return $this->conn->lastInsertId();
        }

        //Ejecuta resultados de una consulta en un array:
        protected function get_results_from_query() {
            $this->open_connection();
	
            $_result = false;
            if (($_stmt = $this->conn->prepare($this->query))) {
                if (preg_match_all('/(:\w+)/', $this->query, $_named, PREG_PATTERN_ORDER)) {
                    $_named = array_pop($_named);
                    foreach ($_named as $_param) {
                    $_stmt->bindValue($_param, $this->parametros[substr($_param, 1)]);
                    }
                }
            
                try {
                    if (! $_stmt->execute()) {
                        //printf("Error de consulta: %s\n", $_stmt->errorInfo()[2]);
                    }
                    //$_result = $_stmt->fetchAll(PDO::FETCH_ASSOC);
                    $this->rows = $_stmt->fetchAll(PDO::FETCH_ASSOC);

                    $_stmt->closeCursor();
                } 
                catch(PDOException $e){
                        //printf("Error en consulta: %s\n" , $e->getMessage());
                }
            }
            //return $_result;
        }
    }
?>
