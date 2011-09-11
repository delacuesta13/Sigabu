<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Usuario extends VanillaModel {
	
	function nuevo ($data) {
		$sql = '
		INSERT INTO usuarios SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql .= 'created_at = NOW()';
		return $this->query($sql);
	}
	
	function eliminar($datos){
	
		$j = 0; ## n�mero de querys exitosos
	
		## construyo las sentencias de eliminaci�n
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea n�mero
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM usuarios WHERE persona_dni = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
	###########################################
	## Roles ##################################
	###########################################
	
	function listar_roles () {
		return $this->query('SELECT * FROM roles ORDER BY permiso DESC');
	}
	
}