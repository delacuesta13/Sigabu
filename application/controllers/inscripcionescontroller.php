<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class InscripcionesController extends VanillaController {
	
	function beforeAction() {
		
		session_start();
		
		/**
		 * El usuario no ha iniciado sesión,
		 * y como las actions de este controlador
		 * son cargadas vía ajax, no renderizar
		 */ 
		if(!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {			
			$this->render = 0;					
		}
		
	}
	
	function listar_inscripciones () {
	
		/****************************************************/
	
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
	
		header("Content-Type: text/html; charset=iso-8859-1");
			
	}
	
	function nuevo ($id_curso = null) {
		
		/**
		 * código que ejecuta javascript del documento que contiene esta vista,
		 * que ha sido cargada vía ajax.
		 * Para saber qué parámetro debe de pasarse, revisar la función js customDialog()
		 * de la vista ver, controlador programacion.
		 */
		$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(\'nuevo-inscripcion\', 0, \'inscripciones\');</script>';
		
		## el usuario tiene permiso para interactuar con la 'action'
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][strtolower($this->_action)]['nivel']) {
			
			## se recibe el id del curso y éste coincide con el patrón
			if (isset($id_curso) && preg_match('/^[\d]{1,}$/', $id_curso)) {
				
				$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
				
				## el curso existe
				if (count($data_curso)!=0) {
					
					## si el usuario es monitor, sólo podrá interactuar con la 'action' si se le asignó el curso a él
					if ($_SESSION['nivel']=='2') {
						$monitor_dni = $_SESSION['persona_dni'];
						if ($monitor_dni!=$data_curso[0]['Curso']['monitor_dni']) {
							$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(\'nuevo-inscripcion\', 1, \'inscripciones\');</script>';
							echo $tag_js;
						} /* if */
					} /* if */
					
					/*******************************************************************************************
				 	 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 	 *******************************************************************************************/
					
					$this->set('id_curso', $id_curso);
					$this->set('data_curso', $data_curso);
					
					/*******************************************************************************************/
					
				} else {
					$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(\'nuevo-inscripcion\', 1, \'inscripciones\');</script>';
					echo $tag_js;
				}
				
			} else {
				$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(\'nuevo-inscripcion\', 1, \'inscripciones\');</script>';
				echo $tag_js;
			} 
			
		} else {
			echo $tag_js;
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	/**
	 * 
	 * recibe los datos del curso
	 * y de la persona que se va a inscribir.
	 * Se procesan dichos datos y se muestra
	 * el resultado del procesamiento.
	 */
	function crear_inscripcion () {
		
		if (isset($_POST['curso'], $_POST['persona'])) {
			
			$validar_data = array(
				'curso' => $_POST['curso'],
				'persona' => $_POST['persona']
			);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_inscripcion($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				$validar_data['curso_id'] = $validar_data['curso'];
				$validar_data['persona_dni'] = $validar_data['persona'];
				
				unset($validar_data['curso'], $validar_data['persona']);
				
				if ($this->Inscripcion->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
			
		} /* datos recibidos */
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	private function validar_data_inscripcion ($datos) {
		
		$ind_error = array();
		
		$dni_format = '/^[\d]{5,20}$/';
		
		## validar dni de la persona
		if (!preg_match($dni_format, $datos['persona']))
			$ind_error['persona'] = 'Ingrese un n&uacute;mero de identificaci&oacute;n.';
		
		## el dni es válido
		else {
			$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['persona']));
			## la persona no existe
			if (count($data_persona)==0) {
				$ind_error['persona'] = 'No existe persona con el n&uacute;mero de identificaci&oacute;n ingresado.';
			} else {
				## la persona no está activa
				if ($data_persona[0]['Persona']['estado']!='1') {
					$ind_error['persona'] = 'La persona no est&aacute; activa';
				} else {
					$data_curso = performAction('programacion', 'consultar_programacion_fk', array($datos['curso']));
					$periodo_id = $data_curso[0]['Curso']['periodo_id'];
					## revisar si la persona tiene un perfil en el periodo del curso
					$sql = '
					SELECT *
					FROM perfiles
					WHERE periodo_id = \'' . $periodo_id . '\'
						  AND persona_dni = \'' . $datos['persona'] . '\' 
					';
					$rs_temp = $this->Inscripcion->query($sql);
					## la persona no tiene un periodo
					if (count($rs_temp)==0) {
						$ind_error['persona'] = 'La persona no tiene perfil en el periodo de la programaci&oacute;n de la actividad.';
					} else {
						## revisar si la persona ya está inscrita en el curso
						$sql = 'SELECT * FROM inscripciones WHERE curso_id = \'' . $datos['curso'] . '\' AND persona_dni = \'' . $datos['persona'] . '\'';
						$rs_temp_2 = $this->Inscripcion->query($sql);
						## la persona ya se inscribió al curso
						if (count($rs_temp_2)!=0) {
							$ind_error['persona'] = 'La persona ya est&aacute; inscrita en la programaci&oacute;n de la actividad.';
						} else {
							
							## horarios del curso en el que la persona se inscribe
							$sql = '
							SELECT dia, hora_inic, hora_fin 
							FROM horarios
							WHERE curso_id = \'' . $datos['curso'] . '\'
							ORDER BY dia ASC, hora_inic ASC
							';
							$horarios_curso = $this->Inscripcion->query($sql);
							
							## consultar los horarios de los cursos en los que la persona se ha inscrito en el periodo
							$sql = '
							SELECT actividad.nombre, horario.dia, horario.hora_inic, horario.hora_fin
							FROM actividades actividad, inscripciones inscripcion, cursos curso, horarios horario
							WHERE inscripcion.persona_dni = \'' . $datos['persona'] . '\'
								  AND inscripcion.curso_id = curso.id
								  AND curso.periodo_id = \'' . $periodo_id . '\'
								  AND curso.actividad_id = actividad.id
								  AND curso.id = horario.curso_id
							ORDER BY horario.dia ASC, actividad.nombre ASC, horario.hora_inic ASC
							';
							$otros_horarios = $this->Inscripcion->query($sql);
							
							$dias = array('Lunes', 'Martes', 'Mi&eacute;rcoles', 'Jueves', 'Viernes', 'S&aacute;bado', 'Domingo');
							$str_temp = '';
							
							for ($i = 0; $i < count($horarios_curso); $i++) {
								for ($j = 0; $j < count($otros_horarios); $j++) {
									
									/*
									 * el día del horario del curso en el que se va a inscribir la persona,
									 * coincide con el día del horario de un curso en el que ya se inscribió
									 */
									if ($horarios_curso[$i]['Horario']['dia']==$otros_horarios[$j]['Horario']['dia']) {
										
										$str_temp = 'El horario del d&iacute;a ' . $dias[intval($horarios_curso[$i]['Horario']['dia']) - 1] . ' (' .
										substr($horarios_curso[$i]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($horarios_curso[$i]['Horario']['hora_fin'], 0, 5) . ') ' .
										' de la programaci&oacute;n de la actividad, se cruza con el horario (' .
										substr($otros_horarios[$j]['Horario']['hora_inic'], 0, 5) . ' - ' . substr($otros_horarios[$j]['Horario']['hora_fin'], 0, 5) . ') ' .
										' -del mismo d&iacute;a- de la actividad ' . $otros_horarios[$j]['Actividad']['nombre'] . 
										', en la que ya est&aacute; inscrita la persona.';
										
										## revisar si se crucen los horarios
										if (strtotime($otros_horarios[$j]['Horario']['hora_inic']) <= strtotime($horarios_curso[$i]['Horario']['hora_inic']) && strtotime($horarios_curso[$i]['Horario']['hora_inic']) <= strtotime($otros_horarios[$j]['Horario']['hora_fin'])) {
											$ind_error['persona'] = $str_temp;
											break;
										} elseif (strtotime($otros_horarios[$j]['Horario']['hora_inic']) <= strtotime($horarios_curso[$i]['Horario']['hora_fin']) && strtotime($horarios_curso[$i]['Horario']['hora_fin']) <= strtotime($otros_horarios[$j]['Horario']['hora_fin'])) {
											$ind_error['persona'] = $str_temp; 
											break;
										} elseif (strtotime($horarios_curso[$i]['Horario']['hora_inic']) <= strtotime($otros_horarios[$j]['Horario']['hora_inic']) && strtotime($otros_horarios[$j]['Horario']['hora_inic']) <= strtotime($horarios_curso[$i]['Horario']['hora_fin'])) {
											$ind_error['persona'] = $str_temp;
											break;
										}
										
									} /* if */
																		
								} /* for j */
							} /* for i */
							
						}
					} /* else */
				} /* else */
			} /* else */
		}
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}