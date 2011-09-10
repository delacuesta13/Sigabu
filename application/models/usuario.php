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
	
	###########################################
	## Roles ##################################
	###########################################
	
	function listar_roles () {
		return $this->query('SELECT * FROM roles');
	}
	
}