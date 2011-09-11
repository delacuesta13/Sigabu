<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
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
	
	function editar ($persona, $data) {
		$sql = '
		UPDATE usuarios SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql = substr_replace($sql, '', -2);
		$sql .= ' WHERE persona_dni = \'' . $persona .'\'';
		return $this->query($sql);
	}
	
	function eliminar($datos){
	
		$j = 0; ## número de querys exitosos
	
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM usuarios WHERE persona_dni = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
	function consultar_usuario ($persona) {
		$sql = '
		SELECT persona.nombres, 
       		   persona.apellidos,
       		   persona.tipo_dni, 
       		   usuario.username, 
       		   usuario.email, 
       		   usuario.rol_id, 
       		   usuario.estado, 
       		   usuario.fecha_activacion 
		FROM   personas	persona, 
       		   usuarios usuario 
		WHERE  usuario.persona_dni = \'' . $persona . '\' 
       		   AND usuario.persona_dni = persona.dni 
		';		
		return $this->query($sql);
	}
	
	###########################################
	## Roles ##################################
	###########################################
	
	function listar_roles () {
		return $this->query('SELECT * FROM roles ORDER BY permiso DESC');
	}
	
}