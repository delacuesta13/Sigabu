<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Horario extends VanillaModel {
	
	function nuevo ($data) {
		$sql = '
		INSERT INTO horarios SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}
	
	/**
	 *
	 * Eliminar horarios ...
	 * @param array $datos
	 */
	function eliminar($datos){
	
		$j = 0; ## número de querys exitosos
	
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM horarios WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
	function consultar_horario ($id) {
		$sql = '
		SELECT actividad.nombre, 
       		   actividad.id, 
       		   area.nombre, 
       		   area.id, 
       		   periodo.periodo, 
       		   periodo.id, 
       		   curso.id, 
       		   horario.dia, 
       		   horario.hora_inic, 
       		   horario.hora_fin, 
       		   lugar.nombre, 
       		   lugar.direccion, 
       		   lugar.id, 
       		   horario.comentario 
		FROM   actividades actividad, 
       		   areas area, 
       		   periodos periodo, 
       		   cursos curso, 
       		   horarios horario, 
       		   lugares lugar 
		WHERE  horario.id = \''. $id . '\' 
       		   AND horario.lugar_id = lugar.id 
       		   AND horario.curso_id = curso.id 
       		   AND curso.actividad_id = actividad.id 
       		   AND actividad.area_id = area.id 
       		   AND curso.periodo_id = periodo.id 
		';
		return $this->query($sql);
	}
	
	/**
	 * 
	 * retorna los horarios de un curso ...
	 * @param int $id_curso
	 */
	function horarios_curso ($id_curso) {
		$sql = '
		SELECT     horario.id,
				   horario.dia,
				   horario.hora_inic,
				   horario.hora_fin,
				   lugar.nombre
		FROM       horarios horario,
			       lugares lugar
		WHERE      horario.curso_id = \'' . $id_curso . '\'
				   AND horario.lugar_id = lugar.id
		ORDER BY   horario.dia ASC,
				   horario.hora_inic ASC
		';
		return $this->query($sql);
	}
	
	function editar($id, $data){
		$sql = '
		UPDATE horarios SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql = substr_replace($sql, '', -2);
		$sql .= ' WHERE id = \'' . $id .'\'';
		return $this->query($sql);
	}
	
}