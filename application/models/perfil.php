<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Perfil extends VanillaModel {

	function nuevo ($data) {
		$sql = '
		INSERT INTO perfiles SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}
	
	function editar($id, $dni, $data){
		$sql = '
		UPDATE perfiles SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql = substr_replace($sql, '', -2);
		$sql .= ' WHERE id = \'' . $id .'\'';
		$sql .= ' AND persona_dni = \'' . $dni .'\'';
		return $this->query($sql);
	}
	
	/**
	 *
	 * Eliminar perfiles ...
	 * @param array $datos
	 */
	function eliminar($datos){

		/**
		 * NOTA: Dado que, para la inscripción de una persona
		 * en una programación de una actividad, es necesario
		 * tener un perfil en el periodo de dicha programación,
		 * al eliminar un perfil (que corresponde a un determinado
		 * periodo) se eliminarán las inscripciones de la persona
		 * en el periodo del perfil. 
		 */
		
		$j = 0; ## número de querys exitosos
			
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				
				/*
				 * obtengo el id del periodo al cual
				 * pertenece el perfil, y la identificación
				 * de la persona a la que se le asignó.
				 */
				$sql = '
				SELECT periodo.id, perfil.persona_dni 
				FROM perfiles perfil, periodos periodo
				WHERE perfil.id = \'' . $datos[$i] . '\' AND perfil.periodo_id = periodo.id';
				$data_perfil = $this->query($sql);
				$id_periodo = $data_perfil[0]['Periodo']['id'];
				$dni_persona = $data_perfil[0]['Perfil']['persona_dni'];
				
				/*
				 * obtengo las inscripciones de los cursos (o programación de actividades)
				 * en los cuales se inscribió la persona en el periodo del perfil.
				 */
				$sql = '
				SELECT inscripcion.id 
				FROM cursos curso, inscripciones inscripcion
				WHERE curso.periodo_id = \'' . $id_periodo . '\' AND curso.id = inscripcion.curso_id
				AND inscripcion.persona_dni = \'' . $dni_persona . '\'';
				$inscripciones = $this->query($sql);
				
				## query exitoso
				if($this->query('DELETE FROM perfiles WHERE id = \'' . $datos[$i] . '\'')){
					$j++;					
					## elimino las inscripciones de la persona en el periodo del perfil
					for ($k = 0; $k < count($inscripciones); $k++) {
						$this->query('DELETE FROM inscripciones WHERE id = \'' . $inscripciones[$k]['Inscripcion']['id'] . '\'');
					} /* for */					
				} /* if query exitoso */
				
			} /* if preg_match */
		} /* for */
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
	function consultar_perfil ($id, $dni) {
		$sql = '
		SELECT periodo.periodo, 
       		   multientidad.nombre 
		FROM   perfiles perfil, 
       		   periodos periodo, 
       		   multientidad multientidad 
		WHERE  perfil.id = \'' .$id . '\'
			   AND perfil.persona_dni = \'' . $dni . '\' 
       		   AND perfil.periodo_id = periodo.id 
       		   AND perfil.perfil_multientidad = multientidad.id 
		';
		return $this->query($sql);
	}
	
	function get_multientidad ($entidad, $id = null) {
		$sql = 'SELECT * FROM multientidad WHERE entidad = \'' . $entidad . '\'';
		if(isset($id))
			$sql .= ' AND id=\'' . $id . '\'';
		$sql .= ' ORDER BY nombre';
		return $this->query($sql);
	}
	
	/**
	 * 
	 * consultar perfil de una persona en un determinado periodo ...
	 * @param int $id_periodo
	 */
	function consultar_perfil_periodo ($dni, $id_periodo) {
		$sql = '
		SELECT perfil.id, 
       		   persona.dni, 
       		   persona.nombres, 
       		   persona.apellidos, 
       		   periodo.periodo, 
       		   multientidad.nombre 
		FROM   perfiles perfil, 
       		   personas persona, 
       		   multientidad multientidad, 
       		   periodos periodo 
		WHERE  periodo.id = \'' . $id_periodo . '\' 
       		   AND periodo.id = perfil.periodo_id 
       		   AND perfil.persona_dni = \'' . $dni . '\'
       		   AND perfil.persona_dni = persona.dni 
       		   AND perfil.perfil_multientidad = multientidad.id 
		';
		return $this->query($sql);
	}
	
	function get_programas () {
		$sql = '
		SELECT programa.id, 
       		   programa.nombre, 
       		   programa.abrev, 
       		   facultad.nombre, 
       		   facultad.abrev 
		FROM   programas programa, 
       		   facultad facultad 
		WHERE  programa.facultad_id = facultad.id 
		';
		return $this->query($sql);
	}
	
}