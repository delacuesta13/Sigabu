<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Asistencia extends VanillaModel {
	
	function nuevo ($datos) {
		$sql = 'INSERT INTO asistencias (inscripcion_id, fecha_asistencia, horario_id) VALUES ';
		for ($i = 0; $i < count($datos['inscripcion_id']); $i++) {
			$sql .= '(' . $datos['inscripcion_id'][$i] . ', \'' . $datos['fecha_asistencia'] . '\', ' . $datos['horario_id'] . '), ';
		}/* for */
		$sql = substr_replace($sql, '', -2);
		return $this->query($sql);
	}
	
	function eliminar($datos){
	
		$j = 0; ## n�mero de querys exitosos
	
		## construyo las sentencias de eliminaci�n
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea n�mero
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM asistencias WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
}