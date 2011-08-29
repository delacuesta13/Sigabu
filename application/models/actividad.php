<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Actividad extends VanillaModel {

	function nuevo ($data) {
		$sql = '
		INSERT INTO actividades SET ';
		foreach ($data as $field => $value){
			$sql .= $field . ' = \'' . $value . '\', ';
		}
		$sql = substr_replace($sql, '', -2);
		return $this->query($sql);
	}
	
	function consultar_actividad ($id) {
		$sql = '
		SELECT act.id, 
       		   act.nombre,
       		   area.id, 
       		   area.nombre, 
       		   act.comentario 
		FROM   actividades act, 
       		   areas area 
		WHERE  act.area_id = area.id 
			   AND act.id = \'' . $id . '\'
		';
		return $this->query($sql);
	}

	function listar_actividades () {
		$sql = '
		SELECT area.id, 
       		   area.nombre, 
       		   actividad.id, 
       		   actividad.nombre 
		FROM   areas area, 
       		   actividades actividad 
		WHERE  area.id = actividad.area_id 
		ORDER  BY area.nombre ASC, 
          	   actividad.nombre ASC 
		';
		return $this->query($sql);
	}
	
	function editar($id, $data){
		$sql = '
		UPDATE actividades SET ';
		foreach ($data as $field => $value) {
			$sql .= $field . ' = ' . ((strlen($value)==0) ? 'NULL' : ' \'' . $value . '\'') . ', ';
		}
		$sql = substr_replace($sql, '', -2);
		$sql .= ' WHERE id = \'' . $id .'\'';
		return $this->query($sql);
	}
	
	/**
	 *
	 * Eliminar actividades ...
	 * @param array $datos
	 */
	function eliminar($datos){
	
		$j = 0; ## número de querys exitosos
	
		## construyo las sentencias de eliminación
		for($i = 0; $i < count($datos); $i++){
			## valido que id sea número
			if(preg_match('/^[\d]{1,}$/', $datos[$i])){
				## query exitoso
				if($this->query('DELETE FROM actividades WHERE id = \'' . $datos[$i] . '\'')){
					$j++;
				}
			}
		}
	
		return (array('trueQuery' => $j, 'totalQuery' => count($datos)));
			
	}
	
	function get_areas () {
		return $this->query('SELECT * FROM areas');
	}
	
}