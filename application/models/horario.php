<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
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
	
		$j = 0; ## n�mero de querys exitosos
	
		## construyo las sentencias de eliminaci�n
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea n�mero
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM horarios WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
}