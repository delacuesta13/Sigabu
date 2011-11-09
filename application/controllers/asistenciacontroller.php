<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class AsistenciaController extends VanillaController {
	
	function beforeAction () {
		
		/**
		 * NOTA: beforeAction(), función que valida
		 * si un usuario tiene el nivel de permiso necesario
		 * para interactuar con una 'action', es efectiva
		 * y sólo valida, cuando la 'action' renderiza,
		 * es decir, cuando tiene su propia vista.
		 */
		
		session_start();
		
		$loginSigabu = performAction('dashboards', 'loginSigabu', array());
		
		if (!$loginSigabu) {
			## destruyo las variables de sesión
			session_unset();
			$_SESSION = array();
			
			## destruyo la sesión actual
			session_destroy();
			
			session_start();
		}
		
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
				//redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
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
	
	function listar_asistencia ($id_curso = null) {
		
		## se recibe el id del curso y éste coincide con el patrón
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
				$campo_dft = 'asistencia.fecha_asistencia';
				$dir_dft = 'desc'; ## dirección de ordenamiento default
				$pag_dft = 1;
				$record_dft = PAGINATE_LIMIT;
				
				### variables que pueden pasarse por medio de parámetros
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
					'asistencias' => array(
						'table' => true,
						'alias' => 'asistencia',
						'fields' => array(
							'id' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							), /* end id */
							'fecha_asistencia' => array(
								'text' => 'Fecha Asistencia',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end fecha_asistencia */
						) /* end fields */
					), /* end asistencias */
					'horarios' => array(
						'table' => true,
						'alias' => 'horario',
						'fields' => array(
							'dia' => array(
								'text' => 'Horario',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end dia */
							'hora_inic' => array(
								'showTable' => false,
								'sort' => false,
								'where' => true
							), /* end hora_inic */
							'hora_fin' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							) /* end hora_fin */
						) /* end fields */
					), /* end horarios */
					'lugares' => array(	
						'table' => true,
						'alias' => 'lugar',
						'fields' => array(
							'nombre' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							) /* end nombre */
						) /* end fields */
					), /* end lugares */
					'personas' => array(
						'table' => true,
						'alias' => 'persona',
						'fields' => array(
							'dni' => array(
								'text' => 'Identificación',
								'showTable' => true,
								'sort' => false,
								'where' => true
							), /* end dni */
							'nombres' => array(
								'text' => 'Nombres',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end nombres */
							'apellidos' => array(
								'text' => 'Apellidos',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end apellidos */
						) /* end fields */
					), /* end personas */
					'join' => array(
						0 => 'curso.id = \'' . $id_curso . '\'',
						1 => 'inscripcion.curso_id = curso.id',
						2 => 'asistencia.inscripcion_id = inscripcion.id',
						3 => 'horario.curso_id = curso.id',
						4 => 'horario.id = asistencia.horario_id',
						5 => 'horario.lugar_id = lugar.id',
						6 => 'inscripcion.persona_dni = persona.dni'
					) /* end join */
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
				$str_tablas_sql = 'FROM cursos curso, inscripciones inscripcion, '; ## tablas de la consulta y sus aliases
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
				$str_temp = 'WHERE (';
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
				$data_query = $this->Asistencia->query($str_query);
				
				## total de tuplas sin LIMIT
				$str_totalquery = 'SELECT FOUND_ROWS() as total';
				$totalreg_query = $this->Asistencia->query($str_totalquery); 
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
			} /* else */
		
		} else {
			## no renderizar
			$this->render = 0;
		} /* else */
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}

	function eliminar ($id_curso = null) {
		
		## el usuario tiene permiso para interactuar con la 'action'
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['nivel']) {
		
			## se recibe el id del curso
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
							$rs = $this->Asistencia->eliminar($_POST['id']);
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
	
	function nuevo ($id_curso = null, $actividad = null) {
		
		$nuevo = false;
		
		## se envió el formulario 
		if (isset($_POST['fecha_asistencia'], $_POST['horario'])) {
			
			$validar_data = array(
				'curso' => $id_curso,
				'fecha_asistencia' => $_POST['fecha_asistencia'],
				'horario' => $_POST['horario'],
			);
			
			## se seleccionaron personas
			if (isset($_POST['personas']))
				$validar_data['personas'] = $_POST['personas'];
			
			## envío los datos a revisión, y recibo los (posibles) errores
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
				 * el id de la inscripción de ésta en el curso,
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
					## agrego el id de la inscripcion a la data que se enviará
					$validar_data['inscripcion_id'][] = $id_inscripcion;
				} /* for */
				
				unset($validar_data['curso'], $validar_data['personas']);
				
				if ($this->Asistencia->nuevo($validar_data)) {
					$nuevo = true;
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
			
		} /* envío del formulario */
		
		$search_caract_array = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', '&', '_');
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
				 *************** Ya aquí empieza el código propio de la 'action' ***************************
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
					
					$( "h2.title" ).append( "<a href=\"" + url + "\">Programación</a> -> Asistencia -> Nuevo" );
						
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
		
		## se creó exitósamente una nueva asistencia
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
		$lista_dias = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');
		
		## validar la fecha de asistencia
		if (!preg_match($fecha_format, $datos['fecha_asistencia']))
			$ind_error['fecha_asistencia'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		/*
		 * verificar que la fecha de asistencia esté dentro
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
			## la fecha no está dentro del rango de fechas del periodo
			if (count($this->Asistencia->query($sql_temp))==0) 
				$ind_error['fecha_asistencia'] = 'La fecha de asistencia no está dentro del rango de fechas del periodo ' . $periodo . '.';
		} /* else */
		
		## validar selección de un horario
		if (!preg_match($select_format, $datos['horario']))
			$ind_error['horario'] = 'Seleccione un horario.';
		
		## validar que el día de la fecha, sea el mismo que el del horario
		elseif (preg_match($fecha_format, $datos['fecha_asistencia'])) {
			## obtengo el día de la fecha de asistencia (1 para lunes, 7 para domingo)
			$tmp_fecha = explode('-', $datos['fecha_asistencia']);
			$dia_asistencia = date('N', mktime(0, 0, 0, $tmp_fecha[1], $tmp_fecha[2], $tmp_fecha[0]));
			
			## obtengo el día del horario
			$dia_horario = performAction('horarios', 'consultar_horario', array($datos['horario']));
			$dia_horario = $dia_horario[0]['Horario']['dia'];
			
			## los días no coinciden
			if ($dia_horario!=$dia_asistencia)
				$ind_error['fecha_asistencia'] = 'El día (' . $lista_dias[intval($dia_asistencia) - 1] . 
				') de la asistencia no coincide con el día (' . $lista_dias[intval($dia_horario) - 1] . ') del horario.';
		} /* elseif */
		
		## validar que se seleccione, por lo menos, una persona
		if (!array_key_exists('personas', $datos) || count($datos['personas'])==0) 
			$ind_error['personas'] = 'Seleccione (por lo menos) una persona.';
		
		## se seleccionaron personas
		else {
			## recorro los dni de las personas
			for ($i = 0; $i < count($datos['personas']); $i++) {
				## el dni en número válido
				if (!preg_match($dni_format, $datos['personas'][$i])) {
					$ind_error['personas'] = 'El número de identificación \'' . $datos['personas'][$i] . '\' no es válido.';
					break;
				} else {
					$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['personas'][$i]));
					## la persona no existe
					if (count($data_persona)==0) {
						$ind_error['personas'] = 'No se encontró persona alguna con número de identificación ' . $datos['personas'][$i] . '.';
						break;
					} elseif ($data_persona[0]['Persona']['estado']!=1) {
						## la persona no está activa
						$ind_error['personas'] = $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'] . 
						' (' . $data_persona[0]['Persona']['tipo_dni'] . ' ' . $data_persona[0]['Persona']['dni'] . ')' . 
						' no está activo (a).';
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