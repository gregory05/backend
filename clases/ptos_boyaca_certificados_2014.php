<?php
include dirname(__FILE__).'/../controladores/data_base.php';

class ptos_boyaca_certificados_2014 {

	  public function __construct() { 
	    	$this->DB= new DB(); 
	  }

	
	function consult_regist(){
			$this->DB->conectarBD();
			$sql = "select * from registro_monetario_copy where validate=1 and valido=1;";																	
			$rs = mysql_query($sql);
			$datos = array();
			if($rs === FALSE){
				echo mysql_error();
				$datos = 0;
			}
			while($row=mysql_fetch_array($rs)) {
				array_push($datos,array("'".$row['tarjeta']."'",$row['cantidad'],$row['fecha'],$row['tipo'],$row['factura'])); 
			}
			//$this->DB->desconectarBD();
			return $datos; 
	}


	function consult_promociones($fecha_re){		
		$this->DB->conectarBD();
		$sql = "SELECT * FROM promociones where fecha_inicio<= '".$fecha_re."' and fecha_fin>='".$fecha_re."';";
		$rs = mysql_query($sql);
		if($row=mysql_fetch_array($rs)) {
			$fc = $row['factor_multi'];
		}else{
			$fc = 1 ;
		}
		//$this->DB->desconectarBD();
		return $fc;
	}
	
	
	function obtener_tarjetas_2014($fecha_actual_){
			$this->DB->conectarBD();
			$ff = date ( 'Y-m-d' );
			$sql = "select tarjeta,
					sum(if(tipo=1,promocion,0)) as compras,
					sum(if(tipo=0,promocion,0)) as devoluciones,
					sum(if(tipo=1,promocion,0)) - sum(if(tipo=0,promocion,0)) as total
					from registro_monetario_copy where valido = 1 and validate = 1
					group by tarjeta order by tarjeta;";										
			$rs = mysql_query($sql);
			$datos = array();
																						
			if($rs === FALSE){
				echo mysql_error();
			}
			//REGISTRO MAYORES A 500 PUNTOS
			while($row=mysql_fetch_array($rs)) {
				if($row['total'] >=500 ){ 
					array_push($datos,array("'".$row['tarjeta']."'",$row['total']));
				}else{
					//SALDO DE PUNTO QUE NO SOBREPASEN LOS 500 SE LOS GUARDARA EN LA TABLA SALDO_PUNTO
					$tarjeta = "'".$row['tarjeta']."'";
					$saldo = $row['total'];
					if ($saldo>0){
					$saldo_punto = $this->saldo_puntos($tarjeta,$saldo,$ff);
					}
				}
			}
			//$this->DB->desconectarBD();
			return $datos; 
			//return $sql;	
	}
	
	
	function consult_client_type($tarjeta){
		$this->DB->conectarBD();
		$sql="select * from usuario_boyaca where tarjeta=".$tarjeta.";";
		$rs = mysql_query($sql);
		if($row=mysql_fetch_array($rs)) {
			$st = $row['status'];
		}
		return $st;
	}
	
	
	function valores_puntos($estado){
		$tipo = 0;
		if($estado=='NORMAL'){$tipo = 0;}
		if($estado=='PLATINUM'){$tipo = 1;}
		$this->DB->conectarBD();
		$sql = "SELECT * FROM valores where tipo = ".$tipo.";";
		$rs = mysql_query($sql);
		while($row=mysql_fetch_array($rs)){						
				$datos = $row['puntos'];
				}			
		return $datos;
	}
	
	
	function actualizar_registro_mone($tarjeta,$tipo,$factura,$valor){
				$this->DB->conectarBD();
				$sql = "update registro_monetario_copy set promocion='".$valor."' where tarjeta = ".$tarjeta." and factura=".$factura." and tipo= ".$tipo." ;";
				$rs = mysql_query($sql);
		//$this->DB->desconectarBD();
		//return $saldo;
	}
	
	
	function valores_certificados(){
			$this->DB->conectarBD();
			$opciones = array();
			$sql = "SELECT * FROM valores where tipo = 2";
			$rs = mysql_query($sql);
			while($row=mysql_fetch_array($rs)){						
				$datos['puntos'] = $row['puntos'];
				}
			//$this->DB->desconectarBD();				
			return $datos;		
		}
	
	function buscar_certificado($cert){
				$r = 0;
				$this->DB->conectarBD();
				$sql = "SELECT * FROM certificados where certificado = ".$cert.";";
				$rs = mysql_query($sql);
				while($row=mysql_fetch_array($rs)){						
						$r = 1;
						}
				//$this->DB->desconectarBD();				
				return $r;				
	}
	
	
	
	function generar_cert(){
			$certificado = '';
			$p = rand(1111111, 7777777);
			$c = rand(2222222, 9999999);
			$certificado = $p.$c;	
			return $certificado;
	}
	

	function crear_nuevo_certificado($tarjeta,$fecha){
				$this->DB->conectarBD();				
				for($y=0;$y<=1;$y++){
					$cert = $this->generar_cert();
					if($this->buscar_certificado($cert)==0){								
								$sql = "insert into certificados_copy (usuario,certificado,fecha_emision,estado) 
								values(".$tarjeta.",'".$cert."','".$fecha."',1)";
						/*$valor =mysql_query("insert into certificados_copy (usuario,certificado,fecha_emision,estado) 
								values(".$tarjeta.",'".$cert."','".$fecha."',1)");
						var_dump($valor);
						echo mysql_error();
						break;*/
				//return	
				mysql_query($sql);						
						$y=1;
					}else {$y=1;}					
				}
				//$this->DB->desconectarBD();		
				//return	$cert;	
		}
		
	
		function consulta_saldo_punto($tarjeta){
				$this->DB->conectarBD();
				$sql = "select saldo from saldo_punto where usuario = ".$tarjeta."";
				$rs = mysql_query($sql);
				if($row=mysql_fetch_array($rs)){	
						$saldo = $row['saldo'];
				}else{
						$saldo = 0;
				}
		//$this->DB->desconectarBD();
		return $saldo;
		}
		
	
		function actualizar_saldo_punto($tarjeta,$saldovar){
				$this->DB->conectarBD();
				$saldo = 0;
				$sql = "update saldo_punto set saldo='".$saldo."' where usuario = ".$tarjeta.";";
				$rs = mysql_query($sql);
		//$this->DB->desconectarBD();
		//return $saldo;
		}
		
		
		function saldo_puntos($tarjeta,$saldo,$fecha_em){
				$this->DB->conectarBD();
				$sql = "select saldo from saldo_punto where usuario = ".$tarjeta."";
				$rs = mysql_query($sql);
				if($row=mysql_fetch_array($rs)){
					$saldo_punto = $row['saldo'];
					$saldo_total = $saldo_punto + $saldo;
					$cert = $this->actualizar_saldo_punto($tarjeta,$saldo_total);
					
				}else{
					$sql = "insert into saldo_punto (usuario,fecha,saldo) 
					values(".$tarjeta.",'".$fecha_em."','".$saldo."')";
					mysql_query($sql);
				}
				//$this->DB->desconectarBD();		
				//return	$sql;	
		}
		
	
		function baja_registros_mone(){
				$this->DB->conectarBD();
				$sql = "update registro_monetario_copy set valido= 3, validate= 2 where valido= 1 and validate= 1 ;";
				$rs = mysql_query($sql);
		$this->DB->desconectarBD();
		//return $saldo;
		}
		
		
		
}
?>