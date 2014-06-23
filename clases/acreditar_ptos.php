<?php
include "acreditar_ptos_clase.php";


$pb = new acreditar_ptos_clase();


$registros = $pb->registros_acreditar();

echo $registros;
?>