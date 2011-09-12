<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Dashboard extends VanillaModel {
	
	function login ($usuario, $password) {
		$sql = '
		SELECT usuario.persona_dni, 
       		   usuario.username, 
       		   rol.permiso, 
       		   usuario.ultima_visita,
       		   usuario.estado,
       		   usuario.fecha_activacion 
		FROM   usuarios usuario, 
       		   roles rol 
		WHERE  usuario.username = \'' . mysql_real_escape_string($usuario) . '\' 
			   AND usuario.password = \'' . mysql_real_escape_string($password) . '\' 
       		   AND usuario.rol_id = rol.id 
		';
		return $this->query($sql);
	}
	
	function ultima_visita ($persona) {
		return $this->query('UPDATE usuarios SET ultima_visita = NOW() WHERE persona_dni = \'' . mysql_real_escape_string($persona) . '\'');		
	}
	
	function activa_usuario ($persona) {
		return $this->query('UPDATE usuarios SET estado = \'1\', fecha_activacion = NULL WHERE persona_dni = \'' . mysql_real_escape_string($persona) . '\' ');
	}
	
}