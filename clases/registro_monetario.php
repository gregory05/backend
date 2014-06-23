<?php
include "regis_monetario_clase.php";
$pb = new regis_monetario_clase();

						$user_offset = '-18000';
						date_default_timezone_set('UTC');
						$diff = "$user_offset seconds";
						if ((substr($diff,0,1) != '+') && (substr($diff,0,1) != '-')) $diff = '+' . $diff;
						$usertime = strtotime($diff, time());
						$fecha = date('Y-m-d H:i:s', $usertime);	
						$fecha_ = date('Ymd', $usertime);	

						$fecha_menos24hs = date('Ymd',$usertime-(24*60*60));
						$archivo_actual = 'PROMO'.$fecha_.'.txt';
						//$archivo_anterior = 'VTA'.$fecha_menos24hs.'.txt';
						//$archivo_anterior = 'VTA20131209.txt';
						$archivo_anterior = 'VTA'.$fecha_.'.txt';
						//echo '<br />';
						//echo $archivo_anterior;

//$archivo = '/nfs/c08/h04/mnt/147221/domains/puntos.boyaca.com/html/archivos_boyaca/'.$archivo_anterior;
//$archivo = '/var/www/vhosts/boyaca.com/puntos.boyaca.com/archivos_boyaca/'.$archivo_anterior;

			//info del archivo
			$fecha_fac_ar = array();
			$local_fac_ar = array();
			$tarjeta_ar = array();
			$fact_ar = array();
			$valor_ar = array();

// ARCHIVO DE PRUEBA
$archivo = '../archivos_boyaca/martes2.txt';	
$lines = file($archivo); 
?> 
<table>
<tr style="text-align:center;background-color:#006EAA;color:#fff;"> 
<td>FECHA_FACTURA</td><td>LOCAL</td><td>TARJETA</td><td>FACTURA</td><td>VALOR</td></tr> 
<?php foreach ($lines as $line_num => $line){  
$datos = explode(";", $line); ?> 
<tr> 
<td> <?php echo $datos[0]; array_push($fecha_fac_ar,$datos[0]); ?> </td>
<td> <?php echo $datos[1]; array_push($local_fac_ar,$datos[1]); ?> </td>
<td> <?php echo $datos[2]; array_push($tarjeta_ar,$datos[2]); ?> </td>
<td> <?php echo $datos[3]; array_push($fact_ar,$datos[3]); ?> </td>
<td> <?php echo $datos[4]; array_push($valor_ar,$datos[4]); ?> </td>
</tr> 
<?php  
}?> 
</table>
<?php
$t = $pb->ingresar_nueva_factura($fecha_fac_ar,$local_fac_ar,$tarjeta_ar,$fact_ar,$valor_ar);
echo $t;
?>

