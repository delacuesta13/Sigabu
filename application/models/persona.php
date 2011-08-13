<?php

class Persona extends VanillaModel {
	
	/**
	 * 
	 * Crear una nueva persona ...
	 * @param array $data
	 */
	function nuevo ($data) {
		$sql = '
		INSERT INTO personas SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}
	
	/**
	 * 
	 * Consultar una persona por su DNI ...
	 * @param array $dni
	 */
	function consultar_persona ($dni) {
		return $this->query('select * from personas where dni = \''. $dni .'\'');
	}
	
	function editar($dni, $data){	
		$sql = '
		UPDATE personas SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql .= 'updated_at = NOW()';
		$sql .= ' WHERE dni = \'' . $dni .'\'';
		return $this->query($sql);
	}
	
	/**
	 * 
	 * Eliminar personas ...
	 * @param array $datos
	 */
	function eliminar($datos){
		
		$j = 0; ## número de querys exitosos
		
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que dni sea número
			if(preg_match('/^[\d]{5,20}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM personas WHERE dni = \'' . $datos[$i] . '\'')){
					$j++;
				}			
			}
		}
		
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
		
	}
	
}