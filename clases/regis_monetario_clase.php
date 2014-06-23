<?php
include dirname(__FILE__).'/../controladores/data_base.php';
class regis_monetario_clase {
	
	  public function __construct() { 
	    	$this->DB= new DB(); 
	  }

	function consult_compra_dev($tarjeta,$factura){
		$this->DB->conectarBD();
		$sql = "SELECT * FROM registro_monetario_copy where tarjeta=".$tarjeta." and factura = '".$factura."'";
		$rs = mysql_query($sql);
		if ($row=mysql_fetch_array($rs)){
			$existe = array (1);
			array_push ($existe, $row['cantidad'], $row['promocion']);
		}else{
			$existe = array (0);
			array_push ($existe, 0, 0);
		}
		return $existe;
	}
	
	function consult_dev_perdida($tarjeta,$factura){
		$datos = array();
		$this->DB->conectarBD();
		$sql = "SELECT * FROM  historico_devolu where tarjeta='".$tarjeta."' and factura = '".$factura."'";
		$rs = mysql_query($sql);
		//$this->DB->desconectarBD();
		return $rs; 
	}
	
	function delete_devol($tarjeta,$factura){
		$this->DB->conectarBD();
		$sql = "DELETE FROM  historico_devolu where tarjeta=".$tarjeta." and factura = '".$factura."'";
		$rs = mysql_query($sql);
		//return $existe;
	}
	
	
	function fecha(){
			$user_offset = '-18000';
			date_default_timezone_set('UTC');
			$diff = "$user_offset seconds";
			if ((substr($diff,0,1) != '+') && (substr($diff,0,1) != '-')) $diff = '+' . $diff;
			$usertime = strtotime($diff, time());
			$fecha = date('Y-m-d H:i:s', $usertime);				
			return $fecha;
		}
	
	function ingresar_nueva_factura($fecha,$local,$tarjeta,$fact,$valor){
		$this->DB->conectarBD();
		$tipo = 1;
		$valores = "";
		$validate = 0;
		$promocion = 0;
		$i_d=1;
		
		for($k=0;$k<=count($fecha)-1;$k++){
			$identi_arr .= $tarjeta[$k];
			// TIPO 1 (COMPRA) - TIPO 2 (DEVOLUCION)					
			if($valor[$k]<0){$tipo = 0;} else {$tipo = 1;};
			
			
			if($tipo==1){
				
				//CONSULTA DE LA DEVOLUCION DE UNA COMPRA PERDIDA
				$consul_devol_perdida = $this->consult_dev_perdida($tarjeta[$k],$fact[$k]);
				
				if($row=mysql_fetch_array($consul_devol_perdida)) {
					$tarjeta_dev = $row['tarjeta'];
					$local_dev = $row['local'];
					$factura_dev = $row['factura'];
					$fecha_devo_dev = $row['fecha_devo'];
					$cantidad_dev = $row['cantidad'];
					
					//INGRESO DE LA DEVOLUCION EN LA TABLA REGISTRO_MONETARIO
					$tipo = 0;//DEVOLUCION
					$promocion_devo = 0;
					//$promocion_devo = $cantidad_dev * $factor_en * $valores;
					$valores_copy.= " ('".$fecha_devo_dev."','".$local_dev."','".$tarjeta_dev."','".$factura_dev."',0,'".$cantidad_dev."','".$tipo."','".$this->fecha()."', '".$promocion_devo."', '".$validate."') , ";
					//ELIMINA DEVOLUCIONES SIN COMPRA DE LA TABLA HISTORIAL_DEVOLUCIONES
					$delete_devol = $this->delete_devol($tarjeta[$k],$fact[$k]);
				}	
				
				$tipo = 1;
				$valores_copy.= " ('".$fecha[$k]."','".$local[$k]."','".$tarjeta[$k]."','".$fact[$k]."',0,'".abs($valor[$k])."','".$tipo."','".$this->fecha()."', '".$promocion."', '".$validate."') , ";	
			}else{
				//CONSULTA DE LA FACTURA  DE COMPRA DE UNA DEVOLUCION Y FACTOR DE PROMOCION
				$result_compra = $this->consult_compra_dev($tarjeta[$k],$fact[$k]); //(promocion / cantidad) = factor_promocion
				if($result_compra[0]==1){
					$promocion = 0;
					$valores_copy.= " ('".$fecha[$k]."','".$local[$k]."','".$tarjeta[$k]."','".$fact[$k]."',0,'".abs($valor[$k])."','".$tipo."','".$this->fecha()."', '".$promocion."', '".$validate."') , ";	
				}else{
					$i_d= 0;
					$valores_devo.= " ('".$tarjeta[$k]."','".$local[$k]."','".$fact[$k]."','".$fecha[$k]."','".$this->fecha()."','".abs($valor[$k])."') , ";
				}								
			}
		}
			$valores_copy.= " ('','','','','','','','','','') ";
			echo $sql_copy = "insert into registro_monetario_copy(fecha,local,tarjeta,factura,valido,cantidad,tipo,fecha_ingreso,promocion,validate) values ".$valores_copy;
			mysql_query($sql_copy);	

			if ($i_d==0){
			$valores_devo.= " ('','','','','','')";
			echo $sql_devo = "insert into historico_devolu(tarjeta,local,factura,fecha_devo,fecha_ingreso,cantidad) values ".$valores_devo;
			mysql_query($sql_devo);	
			}	
		//return $sql;              
		//mysql_query($sql);|
		$sql_del = 'delete from registro_monetario_copy where fecha = "0000-00-00 00:00:00"';
		mysql_query($sql_del);
		$sql_devol = 'delete from historico_devolu where fecha_devo = "0000-00-00 00:00:00"';
		mysql_query($sql_devol);
		//return 'Error: '.mysql_error().'<br/>';	
		echo "-------------";
		echo  ($identi_arr);
		echo "-------------";
		//$this->DB->desconectarBD();				
	}

	
}
?>