<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class ActividadesController extends VanillaController {
	
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
		
		$tag_js = '
		
		var info_preload = \'<div id="info_preload" class="dataTablas_preload">Cargando...</div>\';
		var col = "act.nombre";
		var orderDir = "asc";
		
		function load_dataTable (pag, record, sort, order, search) {		
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_actividades' .'";
				if(pag.length!=0) url += "/pag=" + pag;
				if(record.length!=0) url += "/record=" + record;
				if(sort.length!=0) url += "/sort=" + sort;
				if(order.length!=0) url += "/order=" + order;
				if(search.length!=0) url += "/q=" + encodeURIComponent(search);
				$.ajax({
					url: url,
					success: function(data) {
						$( "#dynamic" ).html(data);
					}
				});	
			});		
		}
		
		$(document).ready(function() {
			load_dataTable(1, ' . PAGINATE_LIMIT . ', col, orderDir, \'\');	
		});	
		
		';
		
		$this->set('make_tag_js', $tag_js);
		
	}
	
	function listar_actividades () {
		
		$parametros = func_get_args();
		
		/**
		 * 
		 * empezar a ordenar por este campo ...
		 * 	<alias>tabla.campo
		 * @var string
		 */
		$campo_dft = 'act.nombre';
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
				'regex' => '/^[a-zA-Z 0-9-]{1,45}$/'
			)
		);
		
		$campos_tabla = array(
			'actividades' => array(
				'table' => true, ## es una tabla el nodo padre de este árbol
				'alias' => 'act',
				'fields' => array(
					'id' => array(
						'showTable' => false, ## mostrar como columna en la tabla			
						'sort' => false, ## puede ordenarse la tabla por este campo
						'where' => false ## buscar por esta columna
					), /* end id */
					'nombre' => array(
						'text' => 'Actividad',
						'showTable' => true,
						'sort' => true,
						'where' => true
					) /* end nombre */
				) /* end fields */
			), /* end actividades */
			'areas' => array(
				'table' => true,
				'alias' => 'area',
				'fields' => array(
					'nombre' => array(
						'text' => 'Área',
						'showTable' => true,
						'sort' => true,
						'where' => true
					) /* end nombre */
				) /* end fields */
			), /* end areas */
			## realizar los joins entre las tablas
			'join' => array(
				0 => 'act.area_id = area.id',
			)
		);
		
		$opciones_data = array(); ## opciones de la consulta
		
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
		$data_query = $this->Actividad->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Actividad->query($str_totalquery); 
		$totalreg_query = $totalreg_query[0]['']['total']; 
		
		/**
		* envío variables a la vista
		*/
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
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}

	function eliminar ($id = null) {
		
		## el usuario tiene permiso para eliminar
		if($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']){
		
			## se recibe un id para eliminar
			if(isset($id) && preg_match('/^[\d]{1,}$/', $id)){
				$rs = $this->Actividad->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Actividad->eliminar($_POST['id']);
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
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function nuevo () {

		## se ha enviado el formulario
		if (isset($_POST['nombre'], $_POST['area'])) {
			
			$validar_data = array(
				'area' => $_POST['area'],
				'nombre' => array(
					'value' => $_POST['nombre'],
					'new' => true,
					'edit' => false
				)
			);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_actividad($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				if(strlen($_POST['comentario'])!=0) $validar_data['comentario'] = addslashes($_POST['comentario']);
				
				$validar_data['nombre'] = $validar_data['nombre']['value'];
				
				$validar_data['area_id'] = $validar_data['area'];
				unset($validar_data['area']);
				
				if ($this->Actividad->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			}
		
		} /* envío del formulario */
		
		$tag_js = '
		$(function() {
			
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			});
			
			$( "h2.title" ).append( "<a href=\"'. BASE_PATH . '/' . strtolower($this->_controller) . '\">Actividades<\/a> -> Nuevo" );

			var options2 = {
				"maxCharacterSize": 200,
				"originalStyle": "originalDisplayInfo",
				"displayFormat": "#left Caracteres Disponibles"
			};
			$("#comentario").textareaCount(options2);
			
		});
		';
		
		$this->set('make_tag_js', $tag_js);

		$this->set('lista_areas', $this->get_areas_fk());
		
		$this->set('makejs', array('jquery.textareaCounter.plugin'));
		
	}
	
	function ver ($id) {
		
		## se recibe un id numérico
		if (preg_match('/^[\d]{1,}$/', $id)) {
			
			$data_actividad = $this->Actividad->consultar_actividad($id);
			##la actividad existe
			if (count($data_actividad)!=0) {
				$this->set('data_actividad', $data_actividad);
			} else {
				$this->set('act_notfound', true);
			}
		
		} else {
			$this->set('act_notfound', true);
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function editar ($id = null, $nombre = null) {
		
		$editar = false;
		
		## se envió el formulario
		if (isset($_POST['nombre'], $_POST['area'])) {
			
			$validar_data = array(
				'area' => $_POST['area'],
				'nombre' => array(
					'value' => $_POST['nombre'],
					'id_act' => $id,
					'new' => false,
					'edit' => true
				)				
			);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_actividad($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				$validar_data['comentario'] = addslashes($_POST['comentario']);
				
				$validar_data['nombre'] = $validar_data['nombre']['value'];
				
				$validar_data['area_id'] = $validar_data['area'];
				unset($validar_data['area']);
				
				if($this->Actividad->editar($id, $validar_data)){
					$editar = true;
				} else {
					$this->set('rs_editar', false);
				}
				
			}
			
		} /* envío del formulario */
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '&', '_');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N', '', '' );
		
		if (isset($id) && preg_match('/^[\d]{1,}$/', $id) && !$editar) {
			
			$data_actividad = $this->Actividad->consultar_actividad($id);
			## la actividad existe
			if (count($data_actividad)!=0) {
				
				$nombre_url = str_replace($search_caract_espec, $replace_caract_espec, $data_actividad[0]['Act']['nombre']);
				$nombre_url = preg_replace('/\s+/', '-', $nombre_url);
				$nombre_url = preg_replace('/-{2,}/', '-', $nombre_url);
				$nombre_url = strtolower($nombre_url);
				## no se recibió nombre, o éste no está como debería
				if (!isset($nombre) || strtolower($nombre)!=$nombre_url) {
					redirectAction(strtolower($this->_controller), $this->_action, array($id, $nombre_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('nombre_url', $nombre_url);
				$this->set('id', $id);
				$this->set('data_actividad', $data_actividad);
								
				$tag_js = '
				$(function() {
							
					$( "h2.title" ).append( "<a href=\"'. BASE_PATH . '/' . strtolower($this->_controller) . '\">Actividades<\/a> -> Editar" );
				
					var options2 = {
						"maxCharacterSize": 200,
						"originalStyle": "originalDisplayInfo",
						"displayFormat": "#left Caracteres Disponibles"
					};
					$("#comentario").textareaCount(options2);
							
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				
				$this->set('lista_areas', $this->get_areas_fk());
				
				$this->set('makejs', array('jquery.textareaCounter.plugin'));
								
			} else {
				redirectAction(strtolower($this->_controller), 'index');	
			}
			
		}
		
		## se editó
		elseif ($editar) {
			redirectAction(strtolower($this->_controller), 'editar', array($id));
		}
		
		## no se recibió nada
		else{
			redirectAction(strtolower($this->_controller), 'index');
		}
		
	}
	
	private function validar_data_actividad ($datos) {
		
		$ind_error = array();

		$letras_format = '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ&_-]{2,60}$/';
		
		## validar el nombre de la actividad
		if(!preg_match($letras_format, $datos['nombre']['value']))
			$ind_error['nombre'] = 'Ingrese sólo letras, número, ampersands (&), guiones (- y _) y espacios.';
		
		elseif (array_key_exists('new', $datos['nombre']) && $datos['nombre']['new']) {
			$tmp_query = $this->Actividad->query('SELECT * FROM actividades WHERE nombre = \'' . mysql_real_escape_string($datos['nombre']['value']) . '\'');
			if(count($tmp_query)!=0)
				$ind_error['nombre'] = 'El nombre de la actividad ya se ha asignado.';
			unset($tmp_query);
		}
		
		elseif (array_key_exists('edit', $datos['nombre']) && $datos['nombre']['edit']) {
			$str_query = 'SELECT * FROM actividades WHERE id != \'' . $datos['nombre']['id_act'] . 
			'\' AND nombre = \'' . mysql_real_escape_string($datos['nombre']['value']) . '\'';
			$tmp_query = $this->Actividad->query($str_query);
			if(count($tmp_query)!=0)
				$ind_error['nombre'] = 'El nombre de la actividad ya se ha asignado.';
			unset($str_query, $tmp_query);
		}
		
		## validar selección de área
		if(!preg_match('/^[\d]{1,}$/', $datos['area']))
			$ind_error['area'] = 'Selecciona el área a la cual pertenece la actividad.';
		
		return $ind_error;
		
	}
	
	##############################################################
	## Áreas #####################################################
	##############################################################
	
	/**
	 * 
	 * Devuelve array con las áreas de BU ...
	 */
	function get_areas_fk () {
		return $this->Actividad->get_areas();
	}
	
	##############################################################
	## Redirecciones #############################################
	##############################################################
	
	function periodos () {
		redirectAction('periodos', 'index');
	}
	
	function programacion () {
		redirectAction('programacion', 'index');
	}
	
	function afterAction() {
	
	}
	
}