<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class HorariosController extends VanillaController {
	
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
	
	/**
	 * 
	 * crear horarios para un curso (o programación de una actividad) ...
	 * @param int $id_curso
	 * @param string $actividad
	 */
	function nuevo ($id_curso = null, $actividad = null) {
		
		## se ha enviado el formaulario
		if (isset($_POST['dia'], $_POST['lugar'], $_POST['hora_inic'], $_POST['hora_fin'])) {
		
			$validar_data = array(
				'curso_id' => $id_curso,
				'dia' => $_POST['dia'],
				'lugar' => $_POST['lugar'],
				'horario' => array(
					'new' => true, ## se va acrear un nuevo horario
					'edit' => false, ## se va a editar un horario
					'hora_inic' => $_POST['hora_inic'],
					'hora_fin' => $_POST['hora_fin']
				) 
			);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_horario($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				/**
				 * asigno valores a key, las cuales
				 * tienen el mismo nombre de los 
				 * campos de la tabla horarios
				 */ 
				$validar_data['lugar_id'] = $validar_data['lugar'];
				$validar_data['hora_inic'] = $validar_data['horario']['hora_inic'];
				$validar_data['hora_fin'] = $validar_data['horario']['hora_fin'];
				
				unset($validar_data['lugar'], $validar_data['horario']);
				
				## ingresó comentario
				if(strlen($_POST['comentario'])!=0) {
					$validar_data['comentario'] = addslashes($_POST['comentario']);
				} else {
					$validar_data['comentario'] = '';
				} 
				
				if ($this->Horario->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
		
		} /* envío del formulario */
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '&', '_');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N', '', '' );
		
		if (isset($id_curso) && preg_match('/^[\d]{1,}$/', $id_curso)) {
			
			$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
			
			## el curso existe
			if (count($data_curso)) {
				
				$actividad_url = strtolower($data_curso[0]['Actividad']['nombre']);
				$actividad_url = str_replace($search_caract_espec, $replace_caract_espec, $actividad_url); ## reemplazo de caracteres
				$actividad_url = preg_replace('/\s+/', '-', $actividad_url); ## reemplazar espacios por guiones
				$actividad_url = preg_replace('/-{2,}/', '-', $actividad_url); ## reemplazar dos o más guiones seguidos, por uno solo
				
				## NO se recibe el nombre de la actividad o NO está como debería aparecer en la URL
				if (!isset($actividad) || $actividad!=$actividad_url) {
					redirectAction(strtolower($this->_controller), 'nuevo', array($id_curso, $actividad_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propio de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('id_curso', $id_curso);
				$this->set('actividad_url', $actividad_url);
				$this->set('data_curso', $data_curso);
				
				$lista_lugares = performAction('lugares', 'listar_lugares_fk', array());
				$this->set('lista_lugares', $lista_lugares);
				
				$tag_js = '
				$(function() {
				
					$("a.cancel").click(function(){
						document.forms["formulario"].reset();
					}); 
				
					var url = "' . BASE_PATH . '/programacion/ver/' . $id_curso . '/' . $actividad_url . '";
					
					$("h2.title").append("<a href=\"" + url + "\">Programación</a> -> Horarios -> Nuevo");
				
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
				
			} else {
				redirectAction('programacion', 'index');
			}
			
		} else {
			redirectAction('programacion', 'index');
		}
		
	}
	
	function listar_horarios ($id_curso = null) {
	    /*
		 * si no se recibe nada, y como ésta es una
		 * 'action' cargada vía ajax, no renderizo
		 * $this->render = 0;
		 */
		
		## se recibe un id de un curso y éste es válido
		if (isset($id_curso) && preg_match('/^\d+$/', $id_curso)) {
		
			## consultar si el curso existe
			$data_curso = performAction('programacion', 'consultar_programacion_fk', array($id_curso));
	
			## el curso existe
			if (count($data_curso)!=0) {
		
				$parametros = func_get_args();
				
				/**
				 *
				 * empezar a ordenar por este campo ...
				 * 	<alias>tabla.campo
				 * @var string
				 */
				$campo_dft = 'horario.dia';
				$dir_dft = 'asc'; ## dirección de ordenamiento default
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
					'horarios' => array(
						'table' => true, ## es una tabla el nodo padre de este árbol
						'alias' => 'horario',
						'fields' => array(
							'id' => array(
								'showTable' => false, ## mostrar como columna en la tabla			
								'sort' => true, ## puede ordenarse la tabla por este campo
								'where' => true ## buscar por esta columna
							), /* end id */
							'dia' => array(
								'text' => 'Día',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end dia */
							'hora_inic' => array(
								'text' => 'Hora Inicio',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end hora_inic */
							'hora_fin' => array(
								'text' => 'Hora Finalización',
								'showTable' => true,
								'sort' => true,
								'where' => true
							) /* end hora_fin */
						) /* end fields */
					), /* end horarios */
					'lugares' => array(
						'table' => true,
						'alias' => 'lugar',
						'fields' => array(
							'nombre' => array(
								'text' => 'Lugar',
								'showTable' => true,
								'sort' => true,
								'where' => true
							), /* end nombre */
							'direccion' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							) /* end direccion */
						) /* end fields */
					), /* end lugares */
					## realizar los joins entre las tablas
					'join' => array(
						0 => 'horario.lugar_id = lugar.id',
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
				$str_tablas_sql = 'FROM '; ## tablas de la consulta y sus aliases
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
				$str_temp = 'WHERE horario.curso_id = \'' . $id_curso . '\' AND (';
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
				$data_query = $this->Horario->query($str_query);
				
				## total de tuplas sin LIMIT
				$str_totalquery = 'SELECT FOUND_ROWS() as total';
				$totalreg_query = $this->Horario->query($str_totalquery); 
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
				} else {
					$this->set('search', '');
				}
				
				unset ($data_query, $totalreg_query, $offset);
				
				/****************************************************/
		
			} else {
				$this->render = 0;
			} /* else */
			
		} else {
			$this->render = 0;
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function ver ($id = null, $id_curso = null, $actividad = null) {
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '&', '_');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N', '', '' );
		
		## se recibieron el id ($id) del horario y el id ($id_curso) del curso
		if (isset($id, $id_curso) && preg_match('/^\d+h$/', $id) && preg_match('/^\d+c$/', $id_curso)) {
		
			$id = preg_replace('/h$/', '', $id);
			$id_curso = preg_replace('/c$/', '', $id_curso);
			
			$data_horario = $this->Horario->consultar_horario($id);
			
			## el horario existe y pertenece al id del curso recibido
			if (count($data_horario)!=0 && $data_horario[0]['Curso']['id']) {
				
				$actividad_url = strtolower($data_horario[0]['Actividad']['nombre']);
				$actividad_url = str_replace($search_caract_espec, $replace_caract_espec, $actividad_url);
				$actividad_url = preg_replace('/\s+/', '-', $actividad_url);
				$actividad_url = preg_replace('/-{2,}/', '-', $actividad_url);
				
				## no se recibe nombre de actividad o éste no está como debería de estar
				if (!isset($actividad) || $actividad!=$actividad_url) {
					redirectAction(strtolower($this->_controller), $this->_action, array($id .'h', $id_curso . 'c', $actividad_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propio de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('id', $id);
				$this->set('id_curso', $id_curso);
				$this->set('actividad_url', $actividad_url);
				$this->set('data_horario', $data_horario);
				
				$tag_js = '
				$(function () {
					var url = "' . BASE_PATH . '/programacion/ver/' . $id_curso . '/' . $actividad . '";
					$("h2.title").append("<a href=\"" + url + "\">Programación</a> -> Horarios -> Ver");
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				
			} else {
				redirectAction('programacion', 'index', array());
			}
		
		} else {
			redirectAction('programacion', 'index', array());
		}
		
	}
	
	function eliminar ($id = null) {
		
	if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']) {
			
			## se recibe un id para eliminar
			if (isset($id) && preg_match('/^[\d]{1,}$/', $id)) {
				$rs = $this->Horario->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Horario->eliminar($_POST['id']);
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## no se recibe nada
			else{
				echo '<div class="message notice"><p>No se ha recibido peticiones.</p></div>';
			}
			
		} else {
			echo '<div class="message warning"><p>Vaya! No tienes el permiso necesario para interactuar con la página solicitada.</p></div>';
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	private function validar_data_horario ($datos) {
		
		$ind_error = array();
		
		$select_format = '/^[\d]{1,}$/';
		$dia_format = '/^[1-7]{1,}$/';
		$hora_format = '/^(([0-1]\d)|(2[0-3])):(([0-5]\d)|(6[1-9]))$/';
		
		## validar selección de un día
		if (!preg_match($dia_format, $datos['dia']))
			$ind_error['dia'] = 'Seleccione un día.';
		
		## validar selección de un lugar
		if (!preg_match($select_format, $datos['lugar']))
			$ind_error['lugar'] = 'Seleccione un lugar.';
		
		## validar formato hora inicio
		if (!preg_match($hora_format, $datos['horario']['hora_inic']))
			$ind_error['hora_inic'] = '(HH:mm) El formato de la hora es incorrecto.';
		
		## validar formato hora finalización
		if (!preg_match($hora_format, $datos['horario']['hora_fin']))
			$ind_error['hora_fin'] = '(HH:mm) El formato de la hora es incorrecto.';
		
		## las horas coinciden con el patrón requerido
		if (preg_match($hora_format, $datos['horario']['hora_inic']) && preg_match($hora_format, $datos['horario']['hora_fin'])) {
			## la hora inicial debe ser menor que la final
			if (strtotime($datos['horario']['hora_inic'])>=strtotime($datos['horario']['hora_fin'])) {
				$ind_error['hora_inic'] = 'La hora de inicio debe ser menor que la hora de finalización.';
			} else{
				$sql_temp = 'SELECT * FROM horarios WHERE curso_id = \'' . $datos['curso_id'] . '\''.
				' AND dia = \'' . $datos['dia'] . '\'';
				## se va editar un horario
				if (array_key_exists('edit', $datos['horario']) && $datos['horario']['edit']) {
					$sql_temp .= ' AND id != \'' . $datos['horario']['id'] . '\'';
				} /* if */
				$sql_temp .= ' ORDER BY hora_inic';
				$rs_temp = $this->Horario->query($sql_temp);
				for ($i = 0; $i < count($rs_temp); $i++) {
					if (strtotime($rs_temp[$i]['Horario']['hora_inic']) <= strtotime($datos['horario']['hora_inic']) && strtotime($datos['horario']['hora_inic']) <= strtotime($rs_temp[$i]['Horario']['hora_fin'])) {
						$ind_error['hora_inic'] = 'El horario a crear se cruza con las horas ' . substr($rs_temp[$i]['Horario']['hora_inic'], 0, 5) . 
						' a ' . substr($rs_temp[$i]['Horario']['hora_fin'], 0, 5) . ', ya asignadas.';
						break;
					} elseif (strtotime($rs_temp[$i]['Horario']['hora_inic']) <= strtotime($datos['horario']['hora_fin']) && strtotime($datos['horario']['hora_fin']) <= strtotime($rs_temp[$i]['Horario']['hora_fin'])) {
						$ind_error['hora_inic'] = 'El horario a crear se cruza con las horas ' . substr($rs_temp[$i]['Horario']['hora_inic'], 0, 5) .
						' a ' . substr($rs_temp[$i]['Horario']['hora_fin'], 0, 5) . ', ya asignadas.';
						break;
					} elseif (strtotime($datos['horario']['hora_inic']) <= strtotime($rs_temp[$i]['Horario']['hora_inic']) && strtotime($rs_temp[$i]['Horario']['hora_inic']) <= strtotime($datos['horario']['hora_fin'])) {
						$ind_error['hora_inic'] = 'El horario a crear se cruza con las horas ' . substr($rs_temp[$i]['Horario']['hora_inic'], 0, 5) .
						' a ' . substr($rs_temp[$i]['Horario']['hora_fin'], 0, 5) . ', ya asignadas.';
						break;
					} /* elseif */
				} /* for */
				unset($sql_temp, $rs_temp);
			} /* else */			
		}
		
		return $ind_error;
		
	} 
	
	function afterAction () {
		
	}
	
}