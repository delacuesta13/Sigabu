<?php

$menu_project = $GLOBALS['menu_project'];

$controlador_actual = $this->_controller;
$accion_actual = $this->_action;

$i = 0;
foreach($menu_project as $controladores => $opciones){
	
	if($_SESSION['nivel'] >= $opciones['nivel']){		
		echo '
		<li'.
		(($i==0) ? (' class="first'. ((strtolower($controlador_actual)==strtolower($controladores)) ? ' active' : '') .'"') :
		((strtolower($controlador_actual)==strtolower($controladores)) ? ' class="active"' : ''))
		.'>'.
		$html->link(ucfirst($controladores), $controladores . '/')
		.'</li>';	

		$i++;
	}	 
	
}

unset($controlador_actual, $accion_actual, $menu_project, $controladores, $opciones, $i);