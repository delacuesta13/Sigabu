<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DashboardsController extends VanillaController {
	
	function beforeAction () {
		
	
	}
	
	function index($tipo_mensaje = null, $nro_mensaje = null) {
		
		/**
		 * El c�digo de revisi�n de usuarios, implementado
		 * en la funci�n beforeAction() de cada controlador, 
		 * valida que el usuario se haya aut�nticado y/o
		 * tenga permiso para interactuar con una action
		 * de un controlador. En dicho c�digo, si el usuario
		 * no ha iniciado o �ste no tiene permiso suficiente,
		 * lo redirecciona a esta action.
		 * 
		 * En relaci�n a lo primero, a continuaci�n se validar� que el
		 * usuario haya iniciado sesi�n, es decir se haya autenticado. 
		 */
		
		session_start();
		
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			redirectAction(strtolower($this->_controller), 'login');			
		}

		#####################################################
		## C�digo propio de la action #######################
		#####################################################
		
		/**
		 * 
		 * Lista de mensajes del proyecto ...
		 * @var array
		 */
		$mensajes_project = array(
			"error" => array(
				"404" => array(
					"text" => "Oops! Al parecer la p�gina que intentas acceder no est� disponible o definitivamente no existe.",
					"tipo" => "notice" ## tipo de mensaje: noticia, warning, error
				),
				"1" => array(
					"text" => "Vaya! No tienes el permiso necesario para interactuar con la p�gina solicitada.",
					"tipo" => "warning"
				),
				"2" => array(
					"text" => "No se puede eliminar usted mismo.",
					"tipo" => "notice"
				)
			)
		);
		
		## Validar si se recibe un mensaje que debe ser mostrado
		if((isset($tipo_mensaje, $nro_mensaje)) && (strlen($tipo_mensaje)!=0 && strlen($nro_mensaje)!=0) && (array_key_exists($tipo_mensaje, $mensajes_project) && array_key_exists($nro_mensaje, $mensajes_project[$tipo_mensaje]))){
			$this->set('showMensaje', array("mensaje" => $mensajes_project[$tipo_mensaje][$nro_mensaje]['text'], "tipo" => $mensajes_project[$tipo_mensaje][$nro_mensaje]['tipo']));			
		}
		
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
	
	function login () {
		
		session_start();

		## env�o del formulario
		if (isset($_POST['usuario'], $_POST['password'])) {
			
			$validar_data = array(
				'usuario' => $_POST['usuario'],
				'password' => $_POST['password']
			);
			
			$usuario_format = performAction('usuarios', 'patron_campos', array('usuario'));
			$password_format = performAction('usuarios', 'patron_campos', array('password'));
			
			## validar el nombre de usuario y el password
			if (!preg_match($usuario_format['regex'], $validar_data['usuario']) || !preg_match($password_format['regex'], $validar_data['password'])) {
				$this->set('error_login', array('type' => 'error', 'message' => 'El nombre usuario o la password son incorrectos.'));
			} else {
				
				## recojo la data del usuario
				$data_usuario = $this->Dashboard->login($validar_data['usuario'], md5($validar_data['password']));
				$loguear = false; ## si valor true -> usuario puede ingresar al sistema
				
				## datos de usuario v�lidos, y estado activo
				if (count($data_usuario)!=0 && $data_usuario[0]['Usuario']['estado']==1) {
					$loguear = true;									
				} elseif (count($data_usuario)!=0 && (strlen($data_usuario[0]['Usuario']['fecha_activacion'])!=0 && $data_usuario[0]['Usuario']['fecha_activacion']!='0000-00-00 00:00:00')) {
					## usuario inactivo y tiene fecha de activaci�n
					$fecha_actual = strtotime(date('Y-m-d H:i:s'));
					$fecha_activacion = strtotime($data_usuario[0]['Usuario']['fecha_activacion']);
					/*
					 * la fecha actual es mayor o igual
					 * que la fecha de activiaci�n, as� 
					 * que activar al usuario y dejarlo
					 * ingresar al sistema.
					 */
					if($fecha_actual >= $fecha_activacion) {
						$this->Dashboard->activa_usuario($data_usuario[0]['Usuario']['persona_dni']);
						$loguear = true;
					}
				} else {
					## usuario o password no existen, o usuario no est� activo
					$this->set('error_login', array('type' => 'error', 'message' => 'El nombre usuario o la password son incorrectos.'));
				}
				
				## el usuario puede loguearse
				if ($loguear) {
					
					## asigno variables a la sesi�n
					$_SESSION['persona_dni'] = $data_usuario[0]['Usuario']['persona_dni'];
					$_SESSION['username'] = $data_usuario[0]['Usuario']['username'];
					$_SESSION['nivel'] = $data_usuario[0]['Rol']['permiso'];
					$_SESSION['logueado'] = true;
					$ultima_visita = $data_usuario[0]['Usuario']['ultima_visita'];
					$_SESSION['ultima_visita'] = (strlen($ultima_visita)!=0 && $ultima_visita!='0000-00-00 00:00:00') ? $ultima_visita : date('Y-m-d H:i');
					
					## registra la fecha de visita actual
					$this->Dashboard->ultima_visita($data_usuario[0]['Usuario']['persona_dni']);
					
				} /* if */
				
				unset($loguear);
				
			} /* else */
			
		} /* env�o del formulario */
		
		/*
		 * si ya inici� sesi�n
		 * lo redirecciona al home del sistema.
		 */
		
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
			redirectAction(strtolower($this->_controller), 'index');
		} else {
			
		}
		
		########################################################################
		
		/**
		 * Como esta es una action de login, no cargo
		 * ni la cabecera ni el pie de p�gina. Para ello
		 * no renderizo y hago uso de la vista como si
		 * fuese una funci�n de respuesta ajax.
		 */
		$this->doNotRenderHeader = 1;
		
	}
	
	function logout (){
		
		session_start();
		
		## destruyo las variables de sesi�n
		session_unset();
		$_SESSION = array();
		## destruyo la sesi�n actual
		session_destroy();
		
		## redirecciono al login
		redirectAction(strtolower($this->_controller), 'login');
		
	}
	
	function afterAction() {
		
	}
	
}