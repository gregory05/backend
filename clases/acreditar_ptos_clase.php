<?php
include dirname(__FILE__).'/../controladores/data_base.php';
class acreditar_ptos_clase {
	
  public function __construct() { 
	    	$this->DB= new DB(); 
	    }
	
	function dias_transcurridos($fecha_i,$fecha_f){
		$dias	= (strtotime($fecha_i)-strtotime($fecha_f))/86400;
		$dias 	= abs($dias); $dias = floor($dias);		
		return $dias;
	}


	function registros_acreditar(){
			$this->DB->conectarBD();
			$fecha_actual = date ( 'Y-m-d' );
			$sql = "SELECT * FROM registro_monetario_copy where valido = 0 and validate = 0 ;";
			$rs = mysql_query($sql);
					while($row=mysql_fetch_array($rs)){	
						$tarjeta = $row['tarjeta'];					
						$fecha_factura = $row['fecha'];
						$dias_dife = $this->dias_transcurridos($fecha_actual,$fecha_factura);
						if($dias_dife>=35){
							$entro = "exito";
							$sql = "update registro_monetario_copy set valido = 1, validate = 1 where tarjeta = '".$tarjeta."'";
							mysql_query($sql);
						}
					}
			return $entro;
	}
}
?>