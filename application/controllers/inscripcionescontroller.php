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
	
	function listar_inscripciones ($id_curso = null) {

		if (isset($id_curso) && preg_match('/^[\d]{1,}$/', $id_curso)) {
			
			$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
			
			## el curso existe
			if (count($data_curso)!=0) {
				
				/****************************************************/
				
				$parametros = func_get_args();
				
				/**
				 *
				 * empezar a ordenar por este campo ...
				 * 	<alias>tabla.campo
				 * @var string
				 */
				$campo_dft = 'inscripcion.fecha_inscripcion';
				$dir_dft = 'desc'; ## dirección de ordenamiento default
				$pag_dft = 1;
				$record_dft = PAGINATE_LIMIT;
				
				## variables que pueden pasarse por medio de parámetros
				$var_data = array(
					## número de página
					'/^pag=/' => array(
						'name' => 'pag',
						'default' => $pag_dft,
						'regex' => '/^[\d]+$/'
					),
					## número de registros por página
					'/^record=/' => array(
						'name' => 'record',
						'default' => $record_dft,
						'regex' => '/^[\d]+$/'
					),
					## columna por la cual ordenar
					'/^sort=/' => array(
						'name' => 'sort',
						'default' => $campo_dft,
						'regex' => '/^[a-zA-Z0-9_\.]+$/'
					),
					## dirección del ordenamiento
					'/^order=/' => array(	
						'name' => 'order',
						'default' => $dir_dft,
						'regex' => '/^(asc|desc)$/'
					),
					## cadena de búsqueda
					'/^q=/' => array(
						'name' => 'search',
						'regex' => '/^[a-zA-Z 0-9-:]{1,45}$/'
					)
				);
				
				$campos_tabla = array(
					'personas' => array(
						'table' => true, ## es una tabla el nodo padre de este árbol
						'alias' => 'persona',
						'fields' => array(
							'dni' => array(
								'text' => 'Identificación',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end dni */
							'tipo_dni' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							), /* end tipo_dni */
							'nombres' => array(
								'text' => 'Nombres',
								'showTable' => true, ## mostrar como columna en la tabla			
								'sort' => true, ## puede ordenarse la tabla por este campo
								'where' => true ## buscar por esta columna
							), /* end nombres */
							'apellidos' => array(
								'text' => 'Apellidos',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end apellidos */
						) /* end fields */
					), /* end personas */
					'multientidad' => array(
						'table' => true,
						'alias' => 'multientidad',
						'fields' => array(	
							'nombre' => array(
								'text' => 'Perfil',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end nombre*/
						) /* end fields */
					), /* end multientidad */
					'inscripciones' => array(
						'table' => true,
						'alias' => 'inscripcion',
						'fields' => array(
							'id' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							), /* end id */
							'fecha_inscripcion' => array(
								'text' => 'Fecha Inscripción',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end fecha_inscripcion */
						), /* end fields */
					), /* end inscripciones */
					## realizar los joins entre las tablas
					'join' => array(
						0 => 'inscripcion.persona_dni = persona.dni',
						1 => 'persona.dni = perfil.persona_dni',
						2 => 'perfil.periodo_id = \'' . $data_curso[0]['Curso']['periodo_id'] . '\'',
						3 => 'perfil.perfil_multientidad = multientidad.id'
					)
				);
				
				/**
				 * recorro los parámetros recibidos,
				 * y si cumplen el respectivo patrón
				 * definido los agrego al SQL de consulta.
				 */
				$str_temp = '';
				for($i = 0; $i < count($parametros); $i++){
					foreach($var_data as $patron => $atributos){
						## el parámetros es un patrón para el SQL
						if(preg_match($patron, $parametros[$i])){
							## valido el valor de la variable que se recibió por parámetro
							$str_temp = preg_replace($patron, '', $parametros[$i]);
							if(preg_match($atributos['regex'], $str_temp)){
								$opciones_data[$atributos['name']] = $str_temp;
							} /* if */
							## como lo que se recibió no coincide con el patrón, asigno valor default
							elseif (array_key_exists('default', $atributos)){
								$opciones_data[$atributos['name']] = $atributos['default'];
							} /* elseif */
						} /* if */
					} /* foreach */
				} /* for */
				unset($str_temp);
				if(isset($patron)) unset($patron);
				if(isset($atributos)) unset($atributos);
				
				/**
				 * inicializo el query de consulta
				 */
				$str_query = 'SELECT SQL_CALC_FOUND_ROWS ';
				
				/**
				 * agrego las columnas al query
				 */
				$str_tablas_sql = 'FROM perfiles perfil, '; ## tablas de la consulta y sus aliases
				foreach ($campos_tabla as $tabla => $def) {
					## $tabla es una tabla
					if(array_key_exists('table', $def) && $def['table']) {
						$str_tablas_sql .= $tabla . ' ' . $def['alias'] . ', ';
						## recorro los campos de la tabla
						foreach($def['fields'] as $field => $attr){
							$str_query .= $def['alias'] . '.' . $field . ', ';
						} /* foreach */
						unset($field, $attr);
					} /* if */
				} /* foreach */
				$str_query = substr_replace($str_query, '', -2) . ' ' . substr_replace($str_tablas_sql, '', -2);
				unset($str_tablas_sql, $tabla, $def);
				
				/**
				 * agrego los joins al query
				 */
				$str_temp = 'WHERE inscripcion.curso_id = \'' . $id_curso . '\' AND (';
				if (array_key_exists('join', $campos_tabla) && is_array($campos_tabla['join']) && count($campos_tabla['join'])!=0) {
					for ($i = 0; $i < count($campos_tabla['join']); $i++) {
						$str_temp .= $campos_tabla['join'][$i] . ' AND ';
					}
				}
				$str_query .= ' ' . substr_replace($str_temp, '', -5) . ')';
				unset($str_temp);
				
				/**
				 * agrego el where a cada una de las columnas
				 */
				if (array_key_exists('search', $opciones_data)) {
					$str_query .= ' AND (';
					foreach ($campos_tabla as $tabla => $def) {
						if (array_key_exists('table', $def) && $def['table']) {
							## recorro los campos de la tabla
							foreach ($def['fields'] as $field => $attr) {
								## se puede buscar por el campo
								if ($attr['where']) {
									$str_query .= $def['alias'] . '.' . $field . ' LIKE \'%' . mysql_real_escape_string($opciones_data['search']) . '%\' OR ';
								} /* if */
							} /* foreach */
							unset($field, $attr);
						} /* if */
					} /* foreach */
					$str_query = substr_replace($str_query, "", -3);
					$str_query .= ')';
					unset($tabla, $def);
				} /* if where */
				
				/**
				 * agrego la columna y la dirección del ordenamiento
				 */
				$j = 0;
				if (array_key_exists('sort', $opciones_data) && array_key_exists('order', $opciones_data)) {
					/**
					 *
					 * 0 -> alias tabla
					 * 1 -> campo ...
					 * @var array
					 */
					$str_temp = explode('.', $opciones_data['sort']);
					foreach ($campos_tabla as $tabla => $def) {
						if (array_key_exists('table', $def) && $def['table'] && strtolower($def['alias'])==strtolower($str_temp[0])) {
							## el campo por el cual ordenar existe en la tabla
							if (array_key_exists(strtolower($str_temp[1]), $def['fields']) && $def['fields'][strtolower($str_temp[1])]['sort']) {
								$str_query .= ' ORDER BY ' . mysql_real_escape_string($opciones_data['sort']) . ' ' . strtoupper(mysql_real_escape_string($opciones_data['order']));
								$j = 1;
							} /* if */
						} /* if */
						if($j == 1) break;
					} /* foreach */
					unset($str_temp, $tabla, $def);
				}
				
				## ordernar y direccionar por default
				if ($j==0) {
					$str_query .= ' ORDER BY ' . $campo_dft . ' ' . strtoupper($dir_dft);
				}
				unset($j);
				
				/**
				 * agrego el limit
				 */
				if (!array_key_exists('pag', $opciones_data)) $opciones_data['pag'] = $pag_dft;
				if (!array_key_exists('record', $opciones_data)) $opciones_data['record'] = $record_dft;
				$offset = $opciones_data['record'] * ($opciones_data['pag'] - 1);
				$str_query .= ' LIMIT '. $offset . ', ' . $opciones_data['record'];
				
				## ejecuto la consulta y recibo las tuplas
				$data_query = $this->Inscripcion->query($str_query);
				
				## total de tuplas sin LIMIT
				$str_totalquery = 'SELECT FOUND_ROWS() as total';
				$totalreg_query = $this->Inscripcion->query($str_totalquery); 
				$totalreg_query = $totalreg_query[0]['']['total'];

				/**
				 * envío variables a la vista
				 */
				$this->set('id_curso', $id_curso);
				$this->set('campos_tabla', $campos_tabla);
				$this->set('data_query', $data_query);
				$this->set('totalreg_query', $totalreg_query);
				$this->set('pagina', $opciones_data['pag']);
				$this->set('record', $opciones_data['record']);
				
				if (array_key_exists('sort', $opciones_data) && array_key_exists('order', $opciones_data)) {
					$this->set('sort', $opciones_data['sort']);
					$this->set('order', $opciones_data['order']);
				} else {
					$this->set('sort', $campo_dft);
					$this->set('order', $dir_dft);
				}
				
				if (array_key_exists('search', $opciones_data)) {
					$this->set('search', $opciones_data['search']);
				}
				
				unset ($data_query, $totalreg_query, $offset);
				
				/****************************************************/
				
			} else {
				$this->render = 0;
			}
			
		} else {
			$this->render = 0;
		}
	
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
				
				$persona_dni = $_SESSION['persona_dni'];
				$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
				
				## el curso existe
				if (count($data_curso)!=0) {
					
					## si el usuario es monitor, sólo podrá interactuar con la 'action' si se le asignó el curso a él
					if ($_SESSION['nivel']=='2' && $persona_dni!=$data_curso[0]['Curso']['monitor_dni']) {
							echo $tag_js;
					} else {
						/*******************************************************************************************
						 *************** Ya aquí empieza el código propia de la 'action' ***************************
						 *******************************************************************************************/
						
						$this->set('id_curso', $id_curso);
						$this->set('data_curso', $data_curso);
						
						/*******************************************************************************************/
					}
					
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
	
	function eliminar ($id_curso = null) {
		
		## el usuario tiene permiso para interactuar con la 'action'
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][strtolower($this->_action)]['nivel']) {
			
			## se recibe el id del curso y éste coincide con el patrón
			if (isset($id_curso) && preg_match('/^[\d]{1,}$/', $id_curso)) {
			
				$persona_dni = $_SESSION['persona_dni'];
				$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
				
				## el curso existe
				if (count($data_curso)!=0) {
					
					## si el usuario es monitor, sólo podrá interactuar con la 'action' si se le asignó el curso a él
					if ($_SESSION['nivel']=='2' && $persona_dni!=$data_curso[0]['Curso']['monitor_dni']) {
						echo '<div class="message warning"><p>Vaya! No tienes el permiso necesario para interactuar con la página solicitada.</p></div>';
					} else {
						/*******************************************************************************************
				 	 	 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 	 	 *******************************************************************************************/
					
						## se recibe (n) mediante post, id (s) para eliminar
						if (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
							$rs = $this->Inscripcion->eliminar($_POST['id']);
							echo '<div class="message notice"><p>
							Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
							</p></div>';
						} else{
							echo '<div class="message notice"><p>No se ha recibido peticiones.</p></div>';
						} /* else */
					
						/*******************************************************************************************/
					} 
					
				} else {
					echo '<div class="message warning"><p>Existe un error al cargar la página solicitada.</p></div>';
					echo $tag_js;
				}
			
			} else {
				echo '<div class="message warning"><p>Existe un error al cargar la página solicitada.</p></div>';
			}
			
		} else {
			echo '<div class="message warning"><p>Vaya! No tienes el permiso necesario para interactuar con la página solicitada.</p></div>';
		}
		
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
					## la persona no tiene un perfil
					if (count($rs_temp)==0) {
						$ind_error['persona'] = 'La persona no tiene perfil en el periodo de la programaci&oacute;n de la actividad.';
					} else {
						## revisar si la persona ya está inscrita en el curso
						$sql = 'SELECT * FROM inscripciones WHERE curso_id = \'' . $datos['curso'] . '\' AND persona_dni = \'' . $datos['persona'] . '\'';
						$rs_temp_2 = $this->Inscripcion->query($sql);
						## la persona ya se inscribió al curso
						if (count($rs_temp_2)!=0) {
							$ind_error['persona'] = 'La persona ya est&aacute; inscrita en la programaci&oacute;n de la actividad.';
						} elseif(INSCRIPCIONES_CRUCEHRS) {
							
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