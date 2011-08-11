<?php

/** 
 * calcular_dif_fechas
 * @comment Fecha en formato yyyy-mm-dd
 * @desc Función para calcular la diferencia entre dos fechas.
 * @author Jhon Adrián Cerón (De la Cuesta)
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version 1.0
 * @date 2010-06-21
*/

function calcular_dif_fechas($fecha1,$fecha2){

	//si $fecha1 es mayor a $fecha2, invierto
	if( strtotime($fecha1) > strtotime($fecha2) ){
		$temp = $fecha2;
		$fecha2 = $fecha1;
		$fecha1 = $temp;
	}

	list($y1,$m1,$d1) = explode("-",$fecha1);
	list($y2,$m2,$d2) = explode("-",$fecha2);

	$dif_y = (int)$y2 - (int)$y1; //diferencia de anios

	$dif_m = (int)$m2 - (int)$m1; //diferencia de meses

	if( $dif_m < 0 ){
		$dif_m += 12;
		$dif_y--;
	}

	$dif_d = (int)$d2 - (int)$d1; //diferencia de dias
	if($dif_d<0){
		$dif_m--;
		$m2--; //resto un mes a $fecha2

		//mktime(hora,minuto,segundo,mes,dia,year)

		##total días del mes anterior a $fecha2
		$ttl_d = mktime(0,0,0,$m2,1,$y2);
		$ttl_d = (int)date("t",$ttl_d);//parséo a entero ((int))

		$dif_d = ($ttl_d - $d1)+$d2;
	}

	$salida = array(
		'years' => $dif_y,
		'months' => $dif_m,
		'days' => $dif_d
	);
	
	return $salida;
}