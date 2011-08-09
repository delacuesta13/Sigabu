<?php

class DashboardsController extends VanillaController {
	
	/**
	 *
	 * @author: Jhon Adrián Cerón <jadrian.ceron@gmail.com>
	 */
	
	function beforeAction () {
		
		/*
		 * Implemento código para iniciar sesión automáticamente.
		 */
		
		session_start();		
		
		if(!array_key_exists('logueado', $_SESSION) || (array_key_exists('logueado', $_SESSION) && !$_SESSION['logueado'])){			
			redirectAction('dashboards', 'login');
		}
	
	}
	
	function index($tipo_mensaje = null, $nro_mensaje = null) {
		
		/**
		 * 
		 * Lista de mensajes del proyecto ...
		 * @var array
		 */
		$mensajes_project = array(
			"error" => array(
				"404" => array(
					"text" => "Oops! Al parecer la página que intentas acceder no está disponible o definitivamente no existe.",
					"tipo" => "notice" ## tipo de mensaje: noticia, warning, error
				),
				"1" => array(
					"text" => "Vaya! No tienes el permiso necesario para interactuar con la página solicitada.",
					"tipo" => "warning"
				)
			)
		);
		
		## Validar si se recibe un mensaje que debe ser mostrado
		if((isset($tipo_mensaje, $nro_mensaje)) && (strlen($tipo_mensaje)!=0 && strlen($nro_mensaje)!=0) && (array_key_exists($tipo_mensaje, $mensajes_project) && array_key_exists($nro_mensaje, $mensajes_project[$tipo_mensaje]))){
			$this->set('showMensaje', array("mensaje" => $mensajes_project[$tipo_mensaje][$nro_mensaje]['text'], "tipo" => $mensajes_project[$tipo_mensaje][$nro_mensaje]['tipo']));			
		}
		
		/*******************************************************/
		
		$tag_js = '
		$(function (){
			$("ul.controllerslist li[title]").qtip({
				position: {
					my: "bottom left", 
					at: "top right"
				},
				style: {
					classes: "ui-tooltip-dark"
				}
			});
		});
		';
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min'));
		$this->set('make_tag_js', $tag_js);
		
	}
	
	function login() {
		
		session_start();
		
		$_SESSION['persona_dni'] = 1107064826;
		$_SESSION['username'] = 'De_la_Cuesta_13';
		$_SESSION['nivel'] = 5;
		$_SESSION['ultima_visita'] = date('Y-m-d H:i');
		$_SESSION['logueado'] = true;
		
		redirectAction('dashboards', 'index');
		
	}
	
	function logout(){
	
		session_start();
		session_destroy();
	
		redirectAction('dashboards', 'index');
	}
	
	function afterAction() {
		
	}
	
}