<?php
	require("configDB.php");

	class DB {
	    public $link;

	    public function __construct() {}

	    //Conexion base
		public function conectarBD(){
			$this->link = mysql_connect(DB_HOST, DB_USER, DB_PASS); 
			if (!$this->link) {
				die('Could not connect: ' . mysql_error());
			}		
			mysql_select_db(DB_NAME); //select database
		}
		
		public function desconectarBD(){
			mysql_close($this->link);
		}

	}
?>