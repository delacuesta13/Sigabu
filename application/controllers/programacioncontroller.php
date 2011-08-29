<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * La tabla (en la BD) de este controlador se llama 'cursos' ...
 * @author Jhon Adri�n Cer�n
 *
 */

class ProgramacionController extends VanillaController {
	
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
		
		if (isset($_POST['actividad'], $_POST['periodo'])) {
			
			$validar_data = array(
				'actividad' => $_POST['actividad'],
				'periodo' => $_POST['periodo']
			);
			
			## ingres� monitor
			if (isset($_POST['monitor']) && strlen($_POST['monitor'])!=0)
				$validar_data['monitor'] = $_POST['monitor'];
			
			## ingres� fecha de inicio
			if (isset($_POST['fecha_inic']) && strlen($_POST['fecha_inic'])!=0)
				$validar_data['fecha_inic'] = $_POST['fecha_inic'];
			
			## ingres� fecha de finalizaci�n
			if (isset($_POST['fecha_fin']) && strlen($_POST['fecha_fin'])!=0)
				$validar_data['fecha_fin'] = $_POST['fecha_fin'];
			
			## la programaci�n es abierta
			if (isset($_POST['abierto'])) {	
				$validar_data['abierto'] = 1;
			} else {
				$validar_data['abierto'] = 0;
			}
			
			## env�o los datos a revisi�n, y recibo los (posibles) errores
			$ind_error = $this->validar_data_programacion($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				## ingres� comentario
				if(strlen($_POST['comentario'])!=0) 
					$validar_data['comentario'] = addslashes($_POST['comentario']);
			
				$validar_data['actividad_id'] = $validar_data['actividad'];
				$validar_data['periodo_id'] = $validar_data['periodo'];
				unset($validar_data['actividad'], $validar_data['periodo']);
				
				if (array_key_exists('monitor', $validar_data)) {
					$validar_data['monitor_dni'] = $validar_data['monitor'];
					unset ($validar_data['monitor']);
				}
				
				if ($this->Programacion->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
			
		} /* env�o del formulario */
		
		$lista_periodos = performAction('periodos', 'listar_periodos_group_fk', array());
		$this->set('lista_periodos', $lista_periodos);
		
		$lista_actividades = performAction('actividades', 'listar_actividades_group_fk', array());
		$this->set('lista_actividades', $lista_actividades);
		
		$tag_js = '
		
		function showInfo () {
			$(function() {
				$("#info_programacion_abierta").dialog({
					modal: true,
					autoOpen: true,
					resizable: false,
					width: 600
				});
			});
		}
		
		$(function() {
		
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			}); 
			
			$( "#fecha_inic, #fecha_fin" ).datepicker({
				regional: "es",
				dateFormat: "yy-mm-dd",				
				changeMonth: true,
				changeYear: true,
				showOtherMonths: true,
				selectOtherMonths: false
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
		
		$this->set('makejs', array('jquery.ui.datepicker-es', 'jquery.textareaCounter.plugin'));
		
	}

	private function validar_data_programacion ($datos) {
		
		$ind_error = array();
		
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$dni_format = '/^[\d]{5,20}$/';
		$select_format = '/^[\d]{1,}$/';
		
		## validar la selecci�n de una actividad
		if (!preg_match($select_format, $datos['actividad']))
			$ind_error['actividad'] = 'Seleccione una actividad.';
		
		## validar la selecci�n de un periodo
		if (!preg_match($select_format, $datos['periodo']))
			$ind_error['periodo'] = 'Seleccione un periodo.';
		
		## validar monitor
		if (array_key_exists('monitor', $datos)) {
			## validar que se haya ingresado un n�mero de identificaci�n v�lido
			if (!preg_match($dni_format, $datos['monitor'])) {
				$ind_error['monitor'] = 'Ingrese un n�mero de identificaci�n v�lido';
			} else {
				## validar que el n�mero de identifaci�n exista en la BD
				$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['monitor']));
				if (count($data_persona)==0) {
					$ind_error['monitor'] = 'El n�mero de identificaci�n ingresado no corresponde a una persona del sistema.';
				} else {
					## validar que la persona est� activa
					if ($data_persona[0]['Persona']['estado']!=1) {
						$ind_error['monitor'] = 'El n�mero de identificaci�n corresponde a una persona que no est� activa.';
					} else {
						## validar que la persona sea monitor
						if ($data_persona[0]['Persona']['monitor']!=1)
							$ind_error['monitor'] = 'El n�mero de identificaci�n corresponde a una persona que no es monitor.';
					} /* else */
				} /* else */
				unset ($data_persona);
			}
		} /* validaci�n del monitor */
		
		## validar fecha de inicio de la programaci�n
		if (array_key_exists('fecha_inic', $datos) && !preg_match($fecha_format, $datos['fecha_inic']))
			$ind_error['fecha_inic'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## validar fecha de finalizaci�n
		if (array_key_exists('fecha_fin', $datos) && !preg_match($fecha_format, $datos['fecha_fin']))
			$ind_error['fecha_fin'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## se ingresaron fechas y �stas son correctas
		if (array_key_exists('fecha_inic', $datos) && array_key_exists('fecha_fin', $datos) && preg_match($fecha_format, $datos['fecha_inic']) && preg_match($fecha_format, $datos['fecha_fin'])) {
			## validar que la fecha inicial sea menor que la fecha final
			if (strtotime($datos['fecha_inic']) >= strtotime($datos['fecha_fin'])) {
				$ind_error['fecha_inic'] = 'La fecha inicial debe ser menor que la fecha de finalizaci�n.';
			} else {
				## verificar que las fechas pertenezcan a un determinado periodo
				$sql_temp = 'SELECT * FROM periodos WHERE (';
				$sql_temp .= '(\'' . $datos['fecha_inic'] . '\' BETWEEN fecha_inic AND fecha_fin) AND'; 
				$sql_temp .= '(\'' . $datos['fecha_fin'] . '\' BETWEEN fecha_inic AND fecha_fin)';
				$sql_temp .= ')'; 
				$tmp_query = $this->Programacion->query($sql_temp);
				## las fechas ingresadas no pertenecen a ning�n periodo
				if (count($tmp_query)==0)
					$ind_error['fecha_inic'] = 'Las fechas de la programaci�n deben de pertenecer a un determinado periodo.';
				unset($sql_temp, $tmp_query);
			} /* else */
		}
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}