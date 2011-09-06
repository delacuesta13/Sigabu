<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AsistenciaController extends VanillaController {
	
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
				//redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
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
	
	function nuevo ($id_curso = null, $actividad = null) {
		
		$nuevo = false;
		
		## se envi� el formulario 
		if (isset($_POST['fecha_asistencia'], $_POST['horario'])) {
			
			$validar_data = array(
				'curso' => $id_curso,
				'fecha_asistencia' => $_POST['fecha_asistencia'],
				'horario' => $_POST['horario'],
			);
			
			## se seleccionaron personas
			if (isset($_POST['personas']))
				$validar_data['personas'] = $_POST['personas'];
			
			## env�o los datos a revisi�n, y recibo los (posibles) errores
			$ind_error = $this->validar_data_asistencia($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores 
			else {
				
				$validar_data['horario_id'] = $validar_data['horario'];
				unset ($validar_data['horario']);
				
				/**
				 * dado que, no se guarda el dni de 
				 * la persona en la asistencia, sino
				 * el id de la inscripci�n de �sta en el curso,
				 * obtengo los ids del las inscripciones para cada persona.
				 */
				$validar_data['inscripcion_id'] = array();
				$sql_temp = '';
				
				for ($i = 0; $i < count($validar_data['personas']); $i++) {
					$sql_temp = '
					SELECT id FROM inscripciones WHERE curso_id = \'' . $validar_data['curso'] . '\' ' .
					'AND persona_dni = \'' . $validar_data['personas'][$i] . '\'';
					$id_inscripcion = $this->Asistencia->query($sql_temp);
					$id_inscripcion = $id_inscripcion[0]['Inscripcion']['id'];
					## agrego el id de la inscripcion a la data que se enviar�
					$validar_data['inscripcion_id'][] = $id_inscripcion;
				} /* for */
				
				unset($validar_data['curso'], $validar_data['personas']);
				
				if ($this->Asistencia->nuevo($validar_data)) {
					$nuevo = true;
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
			
		} /* env�o del formulario */
		
		$search_caract_array = array('�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '&', '_');
		$replace_caract_array = array('a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N', '', '');
		
		## se recibe el id del curso y el nombre de la actividad (adaptado para URL)
		if (isset($id_curso, $actividad) && preg_match('/^[\d]{1,}$/', $id_curso) && preg_match('/^[a-z-]{2,}$/', $actividad) && !$nuevo) {
		
			$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
			
			## el curso existe
			if (count($data_curso)!=0) {
				
				/*
				 * si el usuario tiene rol de monitor (nivel 2), 
				 * debe ser el monitor asignado a la actividad
				 * para poder interactuar con esta 'action'.
				 */
				if ($_SESSION['nivel']=='2' && $_SESSION['persona_dni']!=$data_curso[0]['Curso']['monitor_dni']) {
					redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '1'));
				}
				
				$actividad_url = $data_curso[0]['Actividad']['nombre'];
				$actividad_url = str_replace($search_caract_array, $replace_caract_array, $actividad_url);
				$actividad_url = strtolower($actividad_url); 
				$actividad_url = preg_replace('/\s+/', '-', $actividad_url);
				$actividad_url = preg_replace('/-{2,}/', '-', $actividad_url);
				
				## el nombre recibido de la actividad no coincide
				if ($actividad!=$actividad_url) {
					redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
				}
				
				/*******************************************************************************************
				 *************** Ya aqu� empieza el c�digo propio de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('data_curso', $data_curso);
				$this->set('id_curso', $id_curso);
				$this->set('actividad_url', $actividad_url);
				
				$this->set('lista_horarios', performAction('horarios', 'horarios_curso', array($id_curso)));
				$this->set('lista_inscripciones', performAction('inscripciones', 'inscripciones_curso', array($id_curso)));
				
				$tag_js = '
				$(function() {
							
					$( "#fecha_asistencia" ).datepicker({
						regional: "es",
						dateFormat: "yy-mm-dd",				
						changeMonth: true,
						changeYear: true,
						showOtherMonths: true,
						selectOtherMonths: false
					});
					
					$("#personas").chosen();

					var url = "' . BASE_PATH . '/' . 'programacion' . '/' . 'ver' . '/' . $id_curso . '/' . $actividad_url . '"; 
					
					$( "h2.title" ).append( "<a href=\"" + url + "\">Programaci�n</a> -> Asistencia -> Nuevo" );
						
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				
				$this->set('makecss', array('chosen/chosen'));
				$this->set('makejs', array('jquery.ui.datepicker-es', 'chosen/chosen.jquery.min'));
				
				/*******************************************************************************************/
				
			} else {
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			}
		
		} 
		
		## se cre� exit�samente una nueva asistencia
		elseif ($nuevo) {
			redirectAction('programacion', 'ver', array($id_curso, $actividad));
		}
		
		## no se recibieron datos
		else {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
		}
		
	}
	
	private function validar_data_asistencia ($datos) {
		
		$ind_error = array();
		
		$dni_format = '/^[\d]{5,20}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$select_format = '/^[\d]{1,}$/';
		$lista_dias = array('Lunes', 'Martes', 'Mi�rcoles', 'Jueves', 'Viernes', 'S�bado', 'Domingo');
		
		## validar la fecha de asistencia
		if (!preg_match($fecha_format, $datos['fecha_asistencia']))
			$ind_error['fecha_asistencia'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		/*
		 * verificar que la fecha de asistencia est� dentro
		 * del rango de fechas del periodo del curso
		 */
		else {
			## obtengo el periodo del curso
			$periodo = performAction('programacion', 'consultar_programacion_fk', array($datos['curso']));
			$periodo = $periodo[0]['Periodo']['periodo'];
			$sql_temp = '
			SELECT periodo.id
			FROM   periodos periodo, cursos curso
			WHERE  curso.id = \'' . $datos['curso'] . '\' AND curso.periodo_id = periodo.id
				   AND (\'' . $datos['fecha_asistencia'] . '\' BETWEEN periodo.fecha_inic AND periodo.fecha_fin)
			';
			## la fecha no est� dentro del rango de fechas del periodo
			if (count($this->Asistencia->query($sql_temp))==0) 
				$ind_error['fecha_asistencia'] = 'La fecha de asistencia no est� dentro del rango de fechas del periodo ' . $periodo . '.';
		} /* else */
		
		## validar selecci�n de un horario
		if (!preg_match($select_format, $datos['horario']))
			$ind_error['horario'] = 'Seleccione un horario.';
		
		## validar que el d�a de la fecha, sea el mismo que el del horario
		elseif (preg_match($fecha_format, $datos['fecha_asistencia'])) {
			## obtengo el d�a de la fecha de asistencia (1 para lunes, 7 para domingo)
			$tmp_fecha = explode('-', $datos['fecha_asistencia']);
			$dia_asistencia = date('N', mktime(0, 0, 0, $tmp_fecha[1], $tmp_fecha[2], $tmp_fecha[0]));
			
			## obtengo el d�a del horario
			$dia_horario = performAction('horarios', 'consultar_horario', array($datos['horario']));
			$dia_horario = $dia_horario[0]['Horario']['dia'];
			
			## los d�as no coinciden
			if ($dia_horario!=$dia_asistencia)
				$ind_error['fecha_asistencia'] = 'El d�a (' . $lista_dias[intval($dia_asistencia) - 1] . 
				') de la asistencia no coincide con el d�a (' . $lista_dias[intval($dia_horario) - 1] . ') del horario.';
		} /* elseif */
		
		## validar que se seleccione, por lo menos, una persona
		if (!array_key_exists('personas', $datos) || count($datos['personas'])==0) 
			$ind_error['personas'] = 'Seleccione (por lo menos) una persona.';
		
		## se seleccionaron personas
		else {
			## recorro los dni de las personas
			for ($i = 0; $i < count($datos['personas']); $i++) {
				## el dni en n�mero v�lido
				if (!preg_match($dni_format, $datos['personas'][$i])) {
					$ind_error['personas'] = 'El n�mero de identificaci�n \'' . $datos['personas'][$i] . '\' no es v�lido.';
					break;
				} else {
					$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['personas'][$i]));
					## la persona no existe
					if (count($data_persona)==0) {
						$ind_error['personas'] = 'No se encontr� persona alguna con n�mero de identificaci�n ' . $datos['personas'][$i] . '.';
						break;
					} elseif ($data_persona[0]['Persona']['estado']!=1) {
						## la persona no est� activa
						$ind_error['personas'] = $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'] . 
						' (' . $data_persona[0]['Persona']['tipo_dni'] . ' ' . $data_persona[0]['Persona']['dni'] . ')' . 
						' no est� activo (a).';
						break;
					} else {
						/*
						 * verificar si la persona tiene 
						 * una asistencia, con la fecha
						 * de asistencia y el horario
						 * a crear.
						 */
						$sql_temp = '
						SELECT asistencia.id
						FROM asistencias asistencia, inscripciones inscripcion
						WHERE inscripcion.curso_id = \'' . $datos['curso'] . '\' AND inscripcion.persona_dni = \'' . $datos['personas'][$i] . '\'
							  AND inscripcion.id = asistencia.inscripcion_id AND asistencia.fecha_asistencia = \'' . $datos['fecha_asistencia'] . '\'';
						if (count($this->Asistencia->query($sql_temp))!=0) {
							$ind_error['personas'] = 'Ya se ha asignado la asistencia a ' .
							$data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'] .
							' (' . $data_persona[0]['Persona']['tipo_dni'] . ' ' . $data_persona[0]['Persona']['dni'] . ').';
							break;
						} /* if */
					} /* elseif */
				} /* else */			
			} /* for */
		} /* else */
		
		return $ind_error;
		
	} 
	
	function afterAction () {
		
	}
	
}