<?php

class Perfil extends VanillaModel {

	function nuevo ($data) {
		$sql = '
		INSERT INTO perfiles SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = ' . ((strlen($value)==0) ? ' NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql .= 'created_at = NOW()';
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