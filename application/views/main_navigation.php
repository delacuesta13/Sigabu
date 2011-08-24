<?php

$menu_project = $GLOBALS['menu_project'];

$controlador_actual = $this->_controller;
$accion_actual = $this->_action;

$i = 0;
foreach($menu_project as $controladores => $opciones){
	
	/**
	 * mostrar el controlador si el usuario tiene el permiso suficiente
	 * para interactuar con éste, y si se ha definido el controlador para
	 * que sea mostrado en el menú 
	 */
	if($_SESSION['nivel'] >= $opciones['nivel'] && ((array_key_exists('show', $opciones) && $opciones['show']) || !array_key_exists('show', $opciones))){		
		echo '
		<li'.
		(($i==0) ? (' class="first'. ((strtolower($controlador_actual)==strtolower($controladores)) ? ' active' : '') .'"') :
		((strtolower($controlador_actual)==strtolower($controladores)) ? ' class="active"' : ''))
		.'>'.
		$html->link(((array_key_exists('text', $opciones)) ? $opciones['text'] : ucfirst($controladores)), $controladores . '/')
		.'</li>';	

		$i++;
	}	 
	
}

unset($controlador_actual, $accion_actual, $menu_project, $controladores, $opciones, $i);