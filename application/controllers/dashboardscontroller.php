<?php

class DashboardsController extends VanillaController {
	
	function beforeAction () {
		
		/*
		 * Implemento código para iniciar sesión automáticamente.
		 */
		
		session_start();		
		
		if(!array_key_exists('logueado', $_SESSION) || (array_key_exists('logueado', $_SESSION) && !$_SESSION['logueado'])){			
			redirectAction('dashboards', 'login');
		}
	
	}
	
	function index() {
		
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