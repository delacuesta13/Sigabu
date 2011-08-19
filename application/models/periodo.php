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
	
}