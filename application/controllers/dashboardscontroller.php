<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
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
		 * El código de revisión de usuarios, implementado
		 * en la función beforeAction() de cada controlador, 
		 * valida que el usuario se haya auténticado y/o
		 * tenga permiso para interactuar con una action
		 * de un controlador. En dicho código, si el usuario
		 * no ha iniciado o éste no tiene permiso suficiente,
		 * lo redirecciona a esta action.
		 * 
		 * En relación a lo primero, a continuación se validará que el
		 * usuario haya iniciado sesión, es decir se haya autenticado. 
		 */
		
		session_start();
		
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			redirectAction(strtolower($this->_controller), 'login');			
		}

		#####################################################
		## Código propio de la action #######################
		#####################################################
		
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
	
	## editar los datos de la cuenta del usuario actual
	function configuracion () {
		
		session_start();
		
		if (!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {
			redirectAction(strtolower($this->_controller), 'login');
		}
		
		#####################################################
		## Código propio de la action #######################
		#####################################################
		
		$this->set('data_persona', performAction('personas', 'consultar_persona_fk', array($_SESSION['persona_dni'])));
		
		$email = $this->Dashboard->query('SELECT email FROM usuarios WHERE persona_dni = \'' . $_SESSION['persona_dni'] . '\'');
		$email = $email[0]['Usuario']['email'];
		$this->set('email', $email);
		
		$tag_js = '
		$(document).ready(function() {
			
			$("#btn_edit").click(function() {
				$(".edit_pass").toggle();
				var temp = ($("#edit_pass").val()==1) ? 0 : 1;
				$("#edit_pass").val(temp);
			});

			$("#password[title]")
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
			
			showTip(\'password\', 0);
			
			$("#password").keyup(function (){
				showTip(\'password\', 0);
			});
			
			});
			';
		
		$this->set('make_tag_js', $tag_js);
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min', 'usuarios'));
		
	}
	
	/**
	 * recibe los datos, vía ajax,
	 * a editar de la cuenta de usuario
	 */
	function editar_cuenta () {
		
		session_start();
		
		## validar que el usuario haya iniciado sesión
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
		
			## validar que se reciban los datos
			if (isset($_POST['email'], $_POST['edit_pass'], $_POST['old_password'], $_POST['password'], $_POST['confirm_password'])) {
			
				$validar_data = array(
					'email' => $_POST['email'],
					'edit_pass' => $_POST['edit_pass']
				);
				
				## campos del formulario de editar cuenta
				$campos_form = array(
					'email',
					'old_password',
					'password',
					'confirm_password'
				);
				
				## validar los datos recibidos
				$ind_error = array();
				
				## validar email
				if (!filter_var($validar_data['email'], FILTER_VALIDATE_EMAIL))
					$ind_error['email'] = 'Ingrese un email válido.';
				
				## se va a editar la password
				if ($validar_data['edit_pass']=='1') {
					$validar_data['old_password'] = $_POST['old_password'];
					$validar_data['password'] = $_POST['password'];
					$validar_data['confirm_password'] = $_POST['confirm_password'];
					
					$password_format = performAction('usuarios', 'patron_campos', array('password'));
					
					## el password actual ingresado es válido
					if (preg_match($password_format['regex'], $validar_data['old_password'])) {
						## validar que la password actual ingresada sea correcta
						$sql = 'SELECT * FROM usuarios WHERE persona_dni = \'' . $_SESSION['persona_dni'] . '\'';
						$sql .= ' AND password = \'' . mysql_real_escape_string(md5($validar_data['old_password'])) . '\'';
						$pass_correcta = $this->Dashboard->query($sql);
						$pass_correcta = (count($pass_correcta)!=0) ? true : false;
						## la password actual ingresada es correcta
						if ($pass_correcta) {
							## validar la nueva password
							if (!preg_match($password_format['regex'], $validar_data['password'])) {
								## la nueva password ingresada no es correcta
								$ind_error['password'] = $password_format['error'];
							} else {
								## validar que la nueva password y su confirmación coincidan
								if($validar_data['password']!=$validar_data['confirm_password'])
									$ind_error['confirm_password'] = 'La nueva password y su confirmación no coinciden.';
							} /* else */
						} else {
							$ind_error['old_password'] = 'La password actual ingresada no es correcta.';
						} /* else */
					} else {
						$ind_error['old_password'] = 'La password actual ingresada no es correcta.';
					} /* else */
				} /* edit pass */
				
				unset ($validar_data['edit_pass']);
				
				## mensaje que muestra el resultado del procesamiento 
				$div_msj = '';
				
				$tag_js = '
				<script type="text/JavaScript">
				//<![CDATA[
				$(function (){
				';
				
				## se obtuvieron errores al validar
				if (count($ind_error)!=0) {
					/* ubico error es su respectivo span */
					foreach ($ind_error as $campo => $msj) {
						$tag_js .= "$('.error_" . $campo . "').html('" . $msj . "');\n";
						## busco el key del campo que no es válido y lo elimino de los campos que resultan válidos
						$key_temp = array_search($campo, $campos_form);
						unset ($campos_form[$key_temp], $key_temp);
					} /* foreach */
					unset ($campo, $msj);
					$div_msj = '<div class="message warning"><p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p></div>';
				} else {
					## construyo el sql de actualización
					$sql = 'UPDATE usuarios SET email = \'' . mysql_real_escape_string($validar_data['email']) . '\'';
					## actualizar la password
					if (array_key_exists('password', $validar_data)) {
						$sql .= ', password = \'' . mysql_real_escape_string(md5($validar_data['password'])) . '\'';
					} /* if */
					$sql .= ' WHERE persona_dni = \'' . $_SESSION['persona_dni'] . '\'';
					## se actualizó exitósamete
					if ($this->Dashboard->query($sql)) {
						$div_msj = '<div class="message notice">' .
						'<p>Tus datos se han guardado exitósamente.</p>' .
						'</div>';
					} else {
						$div_msj = '<div class="message error">' .
						'<p>Bueno, esto es vergonzoso. Se ha intentado guardar tus datos, pero al parecer existe un error.</p>' .
						'</div>';
					} /* else */
				} /* else */
				
				## límpio los span de los campos que son válidos
				$campos_form = array_values($campos_form);
				for ($i = 0; $i < count($campos_form); $i++) {
					$tag_js .= "$('.error_" . $campos_form[$i] . "').html('');\n";
				}
				
				$tag_js .= '
				});
				//]]>
				</script>';
				
				echo $tag_js . "\n" . $div_msj;
		
			} else {
				echo '<div class="message warning"><p>No se ha recibido peticiones.</p></div>';
			} /* else */
		
		} else {
			$this->render = 0;
		} /* else */
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function login () {
		
		session_start();

		## envío del formulario
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
				
				## datos de usuario válidos, y estado activo
				if (count($data_usuario)!=0 && $data_usuario[0]['Usuario']['estado']==1) {
					$loguear = true;									
				} elseif (count($data_usuario)!=0 && (strlen($data_usuario[0]['Usuario']['fecha_activacion'])!=0 && $data_usuario[0]['Usuario']['fecha_activacion']!='0000-00-00 00:00:00')) {
					## usuario inactivo y tiene fecha de activación
					$fecha_actual = strtotime(date('Y-m-d H:i:s'));
					$fecha_activacion = strtotime($data_usuario[0]['Usuario']['fecha_activacion']);
					/*
					 * la fecha actual es mayor o igual
					 * que la fecha de activiación, así 
					 * que activar al usuario y dejarlo
					 * ingresar al sistema.
					 */
					if($fecha_actual >= $fecha_activacion) {
						$this->Dashboard->activa_usuario($data_usuario[0]['Usuario']['persona_dni']);
						$loguear = true;
					}
				} else {
					## usuario o password no existen, o usuario no está activo
					$this->set('error_login', array('type' => 'error', 'message' => 'El nombre usuario o la password son incorrectos.'));
				}
				
				## el usuario puede loguearse
				if ($loguear) {
					
					## asigno variables a la sesión
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
			
		} /* envío del formulario */
		
		/*
		 * si ya inició sesión
		 * lo redirecciona al home del sistema.
		 */
		
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado']) {
			redirectAction(strtolower($this->_controller), 'index');
		} else {
			
		}
		
		########################################################################
		
		/**
		 * Como esta es una action de login, no cargo
		 * ni la cabecera ni el pie de página. Para ello
		 * no renderizo y hago uso de la vista como si
		 * fuese una función de respuesta ajax.
		 */
		$this->doNotRenderHeader = 1;
		
	}
	
	function logout (){
		
		session_start();
		
		## destruyo las variables de sesión
		session_unset();
		$_SESSION = array();
		## destruyo la sesión actual
		session_destroy();
		
		## redirecciono al login
		redirectAction(strtolower($this->_controller), 'login');
		
	}
	
	function afterAction() {
		
	}
	
}