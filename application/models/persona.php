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
	
}