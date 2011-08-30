<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Programacion extends VanillaModel {
		
	function nuevo ($data) {
		$sql = '
		INSERT INTO cursos SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}	

	function consultar_programacion ($id) {
		$sql = '
		SELECT actividad.nombre, 
       		   curso.actividad_id,
       		   area.id,
       		   area.nombre, 
       		   periodo.periodo, 
       		   curso.periodo_id, 
       		   curso.monitor_dni, 
       		   curso.fecha_inic, 
       		   curso.fecha_fin, 
       		   curso.abierto, 
       		   curso.comentario 
		FROM   cursos curso, 
       		   actividades actividad,
       		   areas area, 
       		   periodos periodo 
		WHERE  curso.id = \'' . $id . '\' 
			   AND curso.actividad_id = actividad.id
			   AND curso.periodo_id = periodo.id
			   AND actividad.area_id = area.id
		';
		return $this->query($sql);
	}

	function total_inscritos ($id) {
		$sql = '
		SELECT persona.genero, 
       		   COUNT(persona.genero) AS inscritos
		FROM   personas persona, 
       		   inscripciones inscripcion 
		WHERE  inscripcion.curso_id = \'' . $id . '\' 
       		   AND inscripcion.persona_dni = persona.dni 
		GROUP  BY persona.genero 
		';
		return $this->query($sql);
	}
	
	function editar($id, $data){
		$sql = '
		UPDATE cursos SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql .= 'updated_at = NOW()';
		$sql .= ' WHERE id = \'' . $id .'\'';
		return $this->query($sql);
	}
	
	function eliminar($datos){
	
		$j = 0; ## número de querys exitosos
	
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM cursos WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
}