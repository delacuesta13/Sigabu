<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Inscripcion extends VanillaModel {
	
	function nuevo ($data) {
		$sql = '
		INSERT INTO inscripciones SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql .= 'fecha_inscripcion = NOW(), ';
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}
	
	function inscripciones_curso ($id_curso) {
		$sql = '
		SELECT     persona.dni,
				   persona.nombres,
				   persona.apellidos
		FROM 	   personas persona,
				   inscripciones inscripcion
		WHERE      inscripcion.curso_id = \'' . $id_curso . '\'
				   AND inscripcion.persona_dni = persona.dni
		ORDER BY   persona.nombres ASC, persona.apellidos ASC				   
		';
		return $this->query($sql);
	}
	
	function eliminar($datos){
	
		$j = 0; ## número de querys exitosos
	
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM inscripciones WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
}