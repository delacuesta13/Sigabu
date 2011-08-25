<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$menu_project = $GLOBALS['menu_project'];

$controlador_actual = $this->_controller;
$accion_actual = $this->_action;

if(array_key_exists($controlador_actual, $menu_project) && (is_array($menu_project[$controlador_actual]) && array_key_exists('actions', $menu_project[$controlador_actual])) && (is_array($menu_project[$controlador_actual]['actions']) && count($menu_project[$controlador_actual]['actions'])!=0)) {
	
	$i = 0;
	foreach($menu_project[$controlador_actual]['actions'] as $acciones => $opciones){
		
		if($_SESSION['nivel'] >= $opciones['nivel'] && $opciones['showMenu']){			
			echo '
			<li'.
			(($i==0) ? (' class="first'. ((strtolower($accion_actual)==strtolower($acciones)) ? ' active' : '') .'"') : 
			((strtolower($accion_actual)==strtolower($acciones)) ? ' class="active"' : ''))
			.'>'.
			$html->link($opciones['text'], $controlador_actual . '/' .$acciones)
			.'</li>';
			
			$i++;
		}
	
	}

	unset($acciones, $opciones, $i);

}

else{
	echo '<li class="active first">'. $html->link('Inicio', $controlador_actual.'/') .'</li>';
}

unset($menu_project, $controlador_actual, $accion_actual);