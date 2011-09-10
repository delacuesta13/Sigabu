<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UsuariosController extends VanillaController {
	
	function beforeAction () {
		
		/**
		 * NOTA: beforeAction(), función que valida
		 * si un usuario tiene el nivel de permiso necesario
		 * para interactuar con una 'action', es efectiva
		 * y sólo valida, cuando la 'action' renderiza,
		 * es decir, cuando tiene su propia vista.
		 */
		
		session_start();
		
		/**
		 * Validar que el usuario tengo el nivel de permiso
		 * necesario para interactuar con la 'action' del
		 * controlador.
		 */
		
		## Validar que haya se haya logueado
		if(array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']==true) {
			
			/**
			 *
			 * Verificar que la 'action' solicitida existe
			 * en el menú del proyecto y tiene un nivel
			 * mínimo de permiso. Si no se ha definido la
			 * 'action' en el menú del proyecto, se infiere
			 * que el nivel de permiso necesario para ésta
			 * es el nivel mínimo exigido por el controlador
			 * donde está dicha 'action'.
			 */
				 	
			## El controlador no se ha definido en el menú del proyecto
			if (!array_key_exists(strtolower($this->_controller), $GLOBALS['menu_project'])){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			}
				
			/*
			 * No se definió la 'action' en el menú de 'actions'
			 * del controlador, o no se definió el nivel de permiso
			 * para la 'action'. Entonces, el nivel de permiso necesario
			 * para interactuar con la 'action' es el nivel de permiso
			 * default del controlador, al cual pertenece la 'action'.
			 */
			elseif ((!array_key_exists($this->_action, $GLOBALS['menu_project'][strtolower($this->_controller)]['actions']) || !array_key_exists('nivel', $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action])) && $_SESSION['nivel'] < $GLOBALS['menu_project'][strtolower($this->_controller)]['nivel']){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '1'));
			}			
					
			/**
			 * Revisar si el nivel de permiso del usuario 
			 * es INsuficiente para interactuar con la 'action'
			 */
			elseif( (array_key_exists($this->_action, $GLOBALS['menu_project'][strtolower($this->_controller)]['actions']) && array_key_exists('nivel', $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action])) && $_SESSION['nivel'] < $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '1'));
			}
					
		}
				
		## El usuario no ha iniciado sesión
		else{
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
		}
		
	}
	
	function index () {
		
	}
	
	function nuevo () {
		
		## se ha envíado el formulario
		if (isset($_POST['persona'], $_POST['usuario'], $_POST['password'], $_POST['confirm_password'], $_POST['email'], $_POST['rol'])) {
			
			$validar_data = array(
				'query' => 'new',
				'persona' => $_POST['persona'],
				'usuario' => $_POST['usuario'],
				'password' => $_POST['password'],
				'confirm_password' => $_POST['confirm_password'],
				'email' => $_POST['email'],
				'rol' => $_POST['rol']
			);
			
			## el usuario estará activo
			if (isset($_POST['estado'])) {
				$validar_data['estado'] = 1;
			} else {
				$validar_data['estado'] = 0;
				## se ingresó una fecha de activación
				if (strlen($_POST['fecha_activacion'])!=0)
					$validar_data['fecha_activacion'] = $_POST['fecha_activacion'];
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_usuario($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);

			## no se recibieron errores
			else {
				
				$validar_data['persona_dni'] = $validar_data['persona'];
				$validar_data['username'] = $validar_data['usuario'];
				$validar_data['rol_id'] = $validar_data['rol'];
				$validar_data['password'] = md5($validar_data['password']);
				
				unset($validar_data['query'], $validar_data['persona'], $validar_data['usuario'], $validar_data['confirm_password'], $validar_data['rol']);
				
				if ($this->Usuario->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
		
		} /* envío del formulario */
		
		$tag_js = '
		
		function showTip (campo, id_tip) {
			$(function () {
				var url = url_project + "usuarios/valida_datos";
				
				$.ajax({
					url: url,
					type: "POST",
					dataType: "json",
					data: { 
						campo: campo,
						valor: $( "#" + campo).val(),
					},
					success: function( response ) {
						var validar = response.response;
						$("#" + campo).attr("title", validar.message);
						
						var style = "dark";
						
						if (validar.type=="error") {
							style = "red";
							/* elimino estilos que pueda tener el tooltip */
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-dark");
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-green");
							$("#ui-tooltip-" + id_tip + "-title").html("Error");
						} else if (validar.type=="success") {
							style = "green";
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-dark");
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-red");
							$("#ui-tooltip-" + id_tip + "-title").html("Válido");
						} else {
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-red");
							$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-green");
							$("#ui-tooltip-" + id_tip + "-title").html("Información");
						}
							$("#ui-tooltip-" + id_tip + " .ui-tooltip-tip").remove();
							$("#ui-tooltip-" + id_tip).addClass("ui-tooltip-" + style);
					}
				});
				
			});
		}
		
		$(document).ready(function() {
			
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			}); 
			
			$( "#fecha_activacion" ).datepicker({
				regional: "es",
				dateFormat: "yy-mm-dd",				
				changeMonth: true,
				changeYear: true,
				showOtherMonths: true,
				selectOtherMonths: false
			});
			
			$("#estado").click(function() {
				$("#div_fecha").toggle();
			});

			$("#usuario[title], #password[title]")
			.qtip({
				content: {
					title: {
						text: "Información",
                  		button: true
					}
				},
				position: {
					my: "left center", 
					at: "right center"
				},
				style: {
					classes: "ui-tooltip-dark"
				},
				show: {
					event: "focus"
				},
				hide: {
      				event: false
   				}
			});
			
			showTip(\'usuario\', 0);
			showTip(\'password\', 1);
			
			$("#usuario").keyup(function (){
				showTip(\'usuario\', 0);			
			});
			
			$("#password").keyup(function (){
				showTip(\'password\', 1);
			});
			
		});
		';
		
		$this->set('make_tag_js', $tag_js);
		
		$this->set('lista_roles', $this->listar_roles());
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min', 'jquery.ui.datepicker-es'));
		
	}
	
	private function validar_data_usuario ($datos) {
		
		$ind_error = array();
		
		$dni_format = '/^[\d]{5,20}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$select_format = '/^[\d]{1,}$/';
		$usuario_format = $this->patron_campos('usuario');
		$password_format = $this->patron_campos('password');
		
		## se va a crear una nueva cuenta de usuario
		if (strtolower($datos['query'])=='new') {
			
			## validar el número de identificación
			if (!preg_match($dni_format, $datos['persona']))
				$ind_error['persona'] = 'Ingrese un número de identificación válido.';
		
			## el número de identificación es válido
			else {
				## recojo los datos de la persona
				$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['persona']));
				## la persona existe
				if (count($data_persona)!=0) {
					## la persona está activa
					if ($data_persona[0]['Persona']['estado']==1) {
						## revisar si ya se le asignó una cuenta de usuario
						if (count($this->Usuario->query('SELECT * FROM usuarios WHERE persona_dni = \'' . mysql_real_escape_string($datos['persona']) . '\''))!=0)
							$ind_error['persona'] = 'Ya se ha asignado una cuenta de usuario a la persona.';
					} else {
						$ind_error['persona'] = 'El número de identificación ingresado corresponde a una persona que no está activa.';
					} /* else */
				} else {
					$ind_error['persona'] = 'El número de identificación ingresado no corresponde a ninguna persona.';
				} /* else */
			} /* else */
			
			## validar el nombre de usuario
			if (!preg_match($usuario_format['regex'], $datos['usuario']))
				$ind_error['usuario'] = $usuario_format['error'];
			
			## el nombre de usuario ingresado es válido
			else {
				## verificar que no exista ya un nombre de usuario igual en el sistema
				if (count($this->Usuario->query('SELECT * FROM usuarios WHERE username = \'' . mysql_real_escape_string($datos['usuario']) . '\''))) {
					$ind_error['usuario'] = 'El nombre de usuario ya se ha asignado.';
				} /* if */
			} /* else */
			
			## validar la password
			if (!preg_match($password_format['regex'], $datos['password']))
				$ind_error['password'] = $password_format['error'];
			
			## se ingresó un password válido
			else {
				## los passwords no coinciden
				if ($datos['password']!=$datos['confirm_password'])
					$ind_error['confirm_password'] = 'Las passwords no coinciden.';
			} /* else */			
			
		} /* new */
		
		## validar email
		if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese un email válido';
		
		## validar selección de un rol de usuario
		if (!preg_match($select_format, $datos['rol']))
			$ind_error['rol'] = 'Seleccione un rol de usuario';
		
		## el usuario estará inactivo
		if ($datos['estado']==0 && array_key_exists('fecha_activacion', $datos) && !preg_match($fecha_format, $datos['fecha_activacion']))
			$ind_error['fecha_activacion'] = '(AAAA-MM-DD) El formato de fecha es incorrecto.';
		
		return $ind_error;
		
	}
	
	/**
	 * 
	 * Esta no es una función que retorne
	 * datos JSON. Sólo retorna la regex
	 * (expresión regular) de un campo de
	 * usuarios.
	 */
	function patron_campos ($campo = null) {
		$regex = array(
			'usuario' => array(
				'regex' => '/^[a-zA-Z0-9_-]{4,16}$/',
				'error' => 'S&oacute;lo caracteres alfan&uacute;mericos y guiones.', ## mensaje si no coincide con el patrón
				'min' => 4, ## mínimo 4 caracteres
				'max' => 16
			),
			'password' => array(
				'regex' => '/^[a-zA-Z0-9\.\!\$\=\+,@#&_-]{8,20}$/',
				'error' => 'S&oacute;lo caracteres alfan&uacute;mericos, guiones y caracteres especiales (.,!@#$&=+).',
				'min' => 8,
				'max' => 20
			)		
		);
		if (isset($campo) && preg_match('/^[a-z]{2,}$/', strtolower($campo)) && array_key_exists(strtolower($campo), $regex)) {
			return $regex[$campo];
		} else {
			return false;
		}
	}
	
	###########################################
	## Roles ##################################
	###########################################
	
	function listar_roles () {
		return $this->Usuario->listar_roles();
	}
	
	###########################################
	## Funciones JSON #########################
	###########################################
	
	/**
	 * Función que valida los datos
	 * de una cuenta de usuario, sea
	 * antes de crear o editar.
	 */
	function valida_datos () {
		
		$datos = array();
		
		if (isset($_POST['campo'], $_POST['valor']) && preg_match('/^[a-z]{2,}$/', strtolower($_POST['campo']))) {
			$campo = strtolower($_POST['campo']);
			$valor = $_POST['valor'];
			
			## validar el campo ususario
			if ($campo == 'usuario') {
				## recibo los datos relativos al campo
				$usuario_format = $this->patron_campos($campo);
				## la longitud del valor recibido está entre el mínimo y el máximo del campo
				if ($usuario_format['min'] <= strlen($valor) && strlen($valor) <= $usuario_format['max']) {
					## el valor del campo es válido
					if (preg_match($usuario_format['regex'], $valor)) {
						$sql = '';
						## el nombre de usuario está en uso
						if (count($this->Usuario->query('SELECT * FROM usuarios WHERE username = \'' . mysql_real_escape_string($valor) . '\''))!=0) {
							$datos['response'] = array(
								'type' => 'error',
								'message' => 'El nombre de usuario ya est&aacute; en uso.'
							);
						} else {
							$datos['response'] = array(
								'type' => 'success',
								'message' => 'El nombre de usuario es v&aacute;lido y no est&aacute; en uso.'
							);
						}
					} else {
						$datos['response'] = array(
							'type' => 'error',
							'message' => $usuario_format['error']
						);
					}					
				} elseif (strlen($valor)==0) {
					$datos['response'] = array(
						'type' => 'default',
						'message' => 'Ingrese un nombre usuario.'
					);
				} else {
					$datos['response'] = array(
						'type' => 'error',
						'message' => 'Este campo debe tener entre ' . $usuario_format['min'] . ' y ' . $usuario_format['max'] .' caracteres.'
					);
				}
			} elseif ($campo == 'password') {
				## recibo los datos relativos al campo
				$password_format = $this->patron_campos($campo);
				## la longitud del password está dentro del rango
				if ($password_format['min'] <= strlen($valor) && strlen($valor) <= $password_format['max']) {
					## el valor del campo coincide con el patrón
					if (preg_match($password_format['regex'], $valor)) {
						$datos['response'] = array(
							'type' => 'success',
							'message' => 'La password es v&aacute;lida.'
						);
					} else {
						$datos['response'] = array(	
							'type' => 'error',
							'message' => $password_format['error']
						);
					}
				} elseif (strlen($valor)==0) {
					$datos['response'] = array(
						'type' => 'default',
						'message' => 'Ingrese una password.'
					);
				} else {
					$datos['response'] = array(	
						'type' => 'error',
						'message' => 'Este campo debe tener entre ' . $password_format['min'] . ' y ' . $password_format['max'] .' caracteres.'
					);
				}		
			} else {
				## no se recibe un campo válido
				$datos['response'] = array(
					'type' => 'error',
					'message' => 'No se recibieron datos.'
				); 
			} /* else */
		} else {
			## no se recibieron los datos necesario para validar
			$datos['response'] = array(
				'type' => 'error',
				'message' => 'No se recibieron datos. '
			); 
		}
		
		/**
 		 * Send as JSON
 		 */
		header("Content-Type: application/json", true);
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		/* Retornar JSON */
		echo json_encode($datos);
		
	}
	
	function afterAction () {
		
	}
	
}