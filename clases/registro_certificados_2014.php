<?php
date_default_timezone_set('UTC');
$ff = date ( 'Y-m-d' );

include "ptos_boyaca_certificados_2014.php";
$pb = new ptos_boyaca_certificados_2014();

//CONSULTA REGISTRO MONETARIO DE CLIENTES LISTOS PARA ACREDITAR
$clientes_targe = $pb->consult_regist();
if ($clientes_targe==NULL){
	echo "NO EXISTEN DATOS";
	}else{
for($t=0;$t<=count($clientes_targe)-1;$t++){
	$tarjeta =  $clientes_targe[$t][0];
	$cantidad =  $clientes_targe[$t][1];
	$fecha =  $clientes_targe[$t][2];
	$tipo =  $clientes_targe[$t][3];
	$factura =  $clientes_targe[$t][4];
	
	//CONSULTA CLIENTE NORMAL / PLATINUM
	$type_cliente = $pb->consult_client_type($tarjeta); 
	
	//CONSULTA PROMOCION
	$promocion = $pb->consult_promociones($fecha);
	
	//VALORES POR TIPO DE CLIENTE
	$valores = $pb->valores_puntos($type_cliente);
	//CALCULO DE PUNTOS*PROMOCION
	$total_punto_promo =  $cantidad * $promocion * $valores;
	
	//ACTUALIZACION  CAMPO DE PROMOCION_PUNTO EN EL REGISTRO MONETARIO
	$actualizar = $pb->actualizar_registro_mone($tarjeta,$tipo,$factura,$total_punto_promo);
}

//TOTAL DE COMPRAS-DEVOLUCIONES POR CLIENTE
$tarjeta_array = $pb->obtener_tarjetas_2014($ff);

for($h=0;$h<=count($tarjeta_array)-1;$h++){
	$tar .=  $tarjeta_array[$h][0].',';
}
$tar = substr($tar,0,strlen($tar)-1);
echo "----";
echo $tar.'<br />';
echo "---";

 //VALOR DEL CERTIFICADO
$val_certificados = $pb->valores_certificados();

// ENLISTA LAS TARJETAS CON SUS VALORES Y STATUS
for($b=0;$b<=count($tarjeta_array)-1;$b++){
	
	echo $tarjeta_array[$b][0];
	echo "-cer-";
	echo $tarjeta_array[$b][1];
	echo "--";
	
	//TARJETA - TOTAL COMPRAS - TIPO DE CLIENTE
	$tarjeta =$tarjeta_array[$b][0];
	$total_dinero = $tarjeta_array[$b][1];
	
	//CONSULTA DE SALDO DE PUNTOS
	$consul_saldos = $pb->consulta_saldo_punto($tarjeta);
	
	if ($consul_saldos==0){
	  	//$puntos_total = $total_dinero*$valores['puntos'];	
		$puntos_total = $total_dinero;
	}else{
   	  	//$puntos = $total_dinero*$valores['puntos'];
	  	$puntos_total = $consul_saldos + $total_dinero ;
		$saldovar = 0;
		$eliminar_punto = $pb->actualizar_saldo_punto($tarjeta,$saldovar);
	} 
	
	
	//CALCULO #CERTIFICADOS 	
	$entero = explode(".",abs($puntos_total)/$val_certificados['puntos']);
	/// SALDO DE PUNTOS
	$saldo_valor= $puntos_total - ($entero[0]*$val_certificados['puntos']);
	
	$num_certi =  $entero[0];

	echo '--';
	echo $saldo_valor;
	echo '--';
	echo $tarjeta;
	echo '<br />';
	
	
	if(($num_certi >= 1) ){	
			for($k=0;$k<=($num_certi)-1;$k++){	
				// GENERAR CERTIFICADOS
				$save = $pb->crear_nuevo_certificado($tarjeta,$ff); 
			}
				if ($saldo_valor > 0){
				// FUNCION QUE MANEJAS EL SALDO DE PUNTOS 
				$pb->saldo_puntos($tarjeta,$saldo_valor,$ff);}
			}
	}
//DAR DE BAJA A TODOS LOS REGISTRO MOENTARIO UTILIZADOS
$pb->baja_registros_mone();

}

?>

