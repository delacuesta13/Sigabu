<?php

class Periodo extends VanillaModel {
	
	function nuevo ($data) {
		$sql = '
		INSERT INTO periodos SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql = substr_replace($sql, '', -2);
		return $this->query($sql);
	}
	
	/**
	 *
	 * Eliminar periodos ...
	 * @param array $datos
	 */
	function eliminar($datos){
	
		$j = 0; ## n�mero de querys exitosos
	
		## construyo las sentencias de eliminaci�n
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea n�mero
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM periodos WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
}