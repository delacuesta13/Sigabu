<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class LugaresController extends VanillaController {
	
	function beforeAction () {
		
		/**
		 * NOTA: beforeAction(), funci�n que valida
		 * si un usuario tiene el nivel de permiso necesario
		 * para interactuar con una 'action', es efectiva
		 * y s�lo valida, cuando la 'action' renderiza,
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
			 * en el men� del proyecto y tiene un nivel
			 * m�nimo de permiso. Si no se ha definido la
			 * 'action' en el men� del proyecto, se infiere
			 * que el nivel de permiso necesario para �sta
			 * es el nivel m�nimo exigido por el controlador
			 * donde est� dicha 'action'.
			 */
				 	
			## El controlador no se ha definido en el men� del proyecto
			if (!array_key_exists(strtolower($this->_controller), $GLOBALS['menu_project'])){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			}
				
			/*
			 * No se defini� la 'action' en el men� de 'actions'
			 * del controlador, o no se defini� el nivel de permiso
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
				
		## El usuario no ha iniciado sesi�n
		else{
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
		}		
		
	}
	
	function index () {
		
	}
	
	function nuevo () {
		
	if (isset($_POST['nombre'], $_POST['direccion'])) {
		
		$validar_data = array(
			'nombre' => array(
				'value' => $_POST['nombre'],
				'new' => true,
				'edit' => false
			),
			'direccion' => $_POST['direccion']
		);
		
		## ingres� administrador
		if (isset($_POST['administrador']) && strlen($_POST['administrador']))
			$validar_data['administrador'] = $_POST['administrador'];
		
		## ingres� email
		if (isset($_POST['email']) && strlen($_POST['email']))
			$validar_data['email'] = $_POST['email'];
		
		## ingres� tel�fono fijo
		if (isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo']))
			$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
		
		## ingres� tel�fono movil
		if (isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil']))
			$validar_data['telefono_movil'] = $_POST['telefono_movil'];
		
		## env�o los datos a revisi�n, y recibo los (posibles) errores
		$ind_error = $this->validar_data_lugar($validar_data);
		if(is_array($ind_error) && count($ind_error)!=0)
			$this->set('ind_error', $ind_error);
		
		## no se recibieron errores
		else {
			
			## ingres� comentario
			if (isset($_POST['comentario']) && strlen($_POST['comentario'])!=0)
				$validar_data['comentario'] = addslashes($_POST['comentario']);
		
			$validar_data['nombre'] = $validar_data['nombre']['value'];
			
			if ($this->Lugar->nuevo($validar_data)) {
				$this->set('rs_crear', true);
			} else {
				$this->set('rs_crear', false);
			}
			
		}
		
	} /* env�o del formulario */
		
	$tag_js = '
		$(function() {
			
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			});
			
			var options2 = {
				"maxCharacterSize": 200,
				"originalStyle": "originalDisplayInfo",
				"displayFormat": "#left Caracteres Disponibles"
			};
			$("#comentario").textareaCount(options2);
			
		});
		';
	
		$this->set('make_tag_js', $tag_js);
		
		$this->set('makejs', array('jquery.textareaCounter.plugin'));
		
	}
	
	private function validar_data_lugar ($datos) {
		
		$ind_error = array();
		
		$nombre_format = '/^[a-zA-Z0-9 ������������\.\(\)&-_]{5,60}$/';
		$direccion_format = '/^[a-zA-Z0-9 ������������\.\(\)#\/&-_]{5,60}$/';
		$letras_format = '/^[a-zA-Z ������������]{6,80}$/';
		$phone_format = '/^[\d]{5,20}$/';
		
		/**
		 * validar nombre del lugar
		 */
		
		## se va a crear un nuevo nombre de un lugar
		if (array_key_exists('new', $datos['nombre']) && $datos['nombre']['new']) {
			if (!preg_match($nombre_format, $datos['nombre']['value'])) {
				$ind_error['nombre'] = 'Ingrese s�lo letras, n�meros, guiones (- y _), puntos (.), ampersands (&), par�ntesis y espacios.';
			} else {
				$tmp_query = $this->Lugar->query('SELECT * FROM lugares WHERE nombre = \'' . mysql_real_escape_string($datos['nombre']['value']) . '\'');
				## ya existe un lugar con el nombre a crear
				if (count($tmp_query)!=0)
					$ind_error['nombre'] = 'El nombre del lugar ya se ha asignado.'; 
				unset($tmp_query);
			} /* else */
		} /* if */

		## se va a editar el nombre de un lugar
		elseif (array_key_exists('edit', $datos['nombre']) && $datos['nombre']['edit']) {
			if (!preg_match($nombre_format, $datos['nombre']['value'])) {
				$ind_error['nombre'] = 'Ingrese s�lo letras, n�meros, guiones (- y _), puntos (.), ampersands (&), par�ntesis y espacios.';
			} else {
				$sql = 'SELECT * FROM lugares WHERE nombre = \'' . mysql_real_escape_string($datos['nombre']['value']) . '\''.
				' AND id != \'' . $datos['nombre']['id_lugar'] . '\'';
				$tmp_query = $this->Lugar->query($sql);
				if (count($tmp_query)!=0)
					$ind_error['nombre'] = 'El nombre del lugar ya se ha asignado.';
				unset($tmp_query, $sql);
			} /* else */
		} /* elseif */
		
		## validar la direcci�n
		if (!preg_match($direccion_format, $datos['direccion']))
			$ind_error['direccion'] = 'Ingrese s�lo letras, n�meros, guiones (- y _), puntos (.), ampersands (&), par�ntesis, numerales (#), barras (/) y espacios.';
		
		## validar el nombre del administrador del lugar
		if (array_key_exists('administrador', $datos) && !preg_match($letras_format, $datos['administrador'])) 
			$ind_error['administrador'] = 'Ingrese s�lo letras y espacios.';
		
		## validar email
		if (array_key_exists('email', $datos) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese una direcci�n de Email v�lida.';
		
		## validar tel�fono fijo
		if(array_key_exists('telefono_fijo', $datos) && !preg_match($phone_format, $datos['telefono_fijo']))
			$ind_error['telefono_fijo'] = 'Ingrese un n�mero de tel�fono v�lido.';
		
		## validar tel�fono movil
		if(array_key_exists('telefono_movil', $datos) && !preg_match($phone_format, $datos['telefono_movil']))
			$ind_error['telefono_movil'] = 'Ingrese un n�mero de tel�fono v�lido.';
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}