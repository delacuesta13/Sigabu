<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class LugaresController extends VanillaController {
	
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
		var col = "nombre";
		var orderDir = "asc";
				
		function load_dataTable (pag, sort, order) {		
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_lugares' .'";
				var q = $( "#search" ).val();
				if(pag.length!=0) url += "/pag=" + pag;
				url += "/record=" + $( "#reg_pag" ).val();
				if(sort.length!=0) url += "/sort=" + sort;
				if(order.length!=0) url += "/order=" + order;
				if(q.length!=0) url += "/q=" + encodeURIComponent(q);
				$.ajax({
					url: url,
					success: function(data) {
						$( "#dynamic" ).html(data);
					}
				});	
			});		
		}
				
		$(document).ready(function() {
			
			load_dataTable(1, col, orderDir);
			
			$( "#reg_pag" ).change(function() {
				load_dataTable(1, col, orderDir);
			});
			
			$( "#search" ).bind("keyup", function() {
				load_dataTable(1, col, orderDir);
			});
					
		});	
		';
		
		$this->set('make_tag_js', $tag_js);
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min'));
	
	}

	function listar_lugares () {
		
		$parametros = func_get_args();
		
		$tabla = strtolower($this->_controller); ## tabla del controlador en la BD
		$campo_dft = 'nombre'; ## empieza a ordenar por este campo
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
				'regex' => '/^[a-zA-Z0-9_]+$/'
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
			'id' => array(
				'showTable' => false, ## mostrar como columna en la tabla
				'sort' => false, ## puede ordenarse la tabla por este campo
				'where' => false ## buscar por esta columna
			),
			'nombre' => array(
				'text' => 'Nombre',
				'showTable' => true,
				'sort' => true,
				'where' => true
			),
			'direccion' => array(
				'text' => 'Dirección',
				'showTable' => true,
				'sort' => true,
				'where' => true
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
		foreach($campos_tabla as $campos => $def){
			$str_query .= $campos . ', ';
		}
		$str_query = substr_replace($str_query, '', -2);
		
		unset($campos, $def);
		
		$str_query .= ' FROM ' . $tabla . ' ';
		
		/**
		 * agrego el where a cada una de las columnas
		 */
		if(array_key_exists('search', $opciones_data)){
			$str_query .= 'WHERE (';
			foreach($campos_tabla as $campos => $def){
				## se puede buscar utilizando el campo
				if($def['where']){
					$str_query .= $campos . ' LIKE \'%' . mysql_real_escape_string($opciones_data['search']) . '%\' OR ';
				} /* if */
			} /* foreach */
			$str_query = substr_replace($str_query, "", -3);
			$str_query .= ')';
			unset($campos, $def);
		} /* if where */
		
		/**
		 * agrego la columna y la dirección del ordenamiento
		 */
		if(array_key_exists('sort', $opciones_data) && array_key_exists('order', $opciones_data) && $campos_tabla[strtolower(mysql_real_escape_string($opciones_data['sort']))]['sort']){
			$str_query .= ' ORDER BY ' . mysql_real_escape_string($opciones_data['sort']) . ' ' . strtoupper(mysql_real_escape_string($opciones_data['order']));
		} else {
			$str_query .= ' ORDER BY ' . $campo_dft . ' ' . $dir_dft;
		}
		
		/**
		 * agrego el limit
		 */
		if (!array_key_exists('pag', $opciones_data)) $opciones_data['pag'] = $pag_dft;
		if (!array_key_exists('record', $opciones_data)) $opciones_data['record'] = $record_dft;
		$offset = $opciones_data['record'] * ($opciones_data['pag'] - 1);
		$str_query .= ' LIMIT '. $offset . ', ' . $opciones_data['record'];

		## ejecuto la consulta y recibo las tuplas
		$data_query = $this->Lugar->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Lugar->query($str_totalquery);
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
		
		unset ($data_query, $totalreg_query, $offset);
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function eliminar ($id = null) {
		
		## el usuario tiene permiso para eliminar
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']) {
		
			## se recibe un id para eliminar
			if(isset($id) && preg_match('/^[\d]{1,}$/', $id)){
				$rs = $this->Lugar->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Lugar->eliminar($_POST['id']);
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
	
	function ver ($id = null, $nombre = null) {
		
		$search_array = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', '.', '(', ')', '&', '-', '_');
		$replace_array = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n', '', '-', '-', '', '-', '-');
		
		## validar que se reciba un id numérico
		if (isset($id) && preg_match('/^[\d]{1,}$/', $id)) {
		
			$data_lugar = $this->Lugar->consultar_lugar($id);

			## verificar que existe un lugar con el id recibido
			if (count($data_lugar)!=0) {
				
				$nombre_url = strtolower($data_lugar[0]['Lugar']['nombre']);
				$nombre_url = str_replace($search_array, $replace_array, $nombre_url);
				
				$nombre_url = preg_replace('/\s+/', '-', $nombre_url);
				$nombre_url = preg_replace('/-{2,}/', '-', $nombre_url);
				
				## validar que el nombre recibido está como debería de estar en una URL
				if (!isset($nombre) || $nombre!=$nombre_url) {
					redirectAction(strtolower($this->_controller), $this->_action, array($id, $nombre_url));
				} 
					
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 *******************************************************************************************/
					
				$this->set('id', $id);
				$this->set('nombre_url', $nombre_url);
				$this->set('data_lugar', $data_lugar);
				
				$tag_js = '
				$(function () {
					$("h2.title").append("Ver");
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				
			} else {
				redirectAction(strtolower($this->_controller), 'index');
			}
			
		} else {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
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
		
			## ingresó administrador
			if (isset($_POST['administrador']) && strlen($_POST['administrador'])!=0)
				$validar_data['administrador'] = $_POST['administrador'];
		
			## ingresó email
			if (isset($_POST['email']) && strlen($_POST['email'])!=0)
				$validar_data['email'] = $_POST['email'];
		
			## ingresó teléfono fijo
			if (isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo'])!=0)
				$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
		
			## ingresó teléfono movil
			if (isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil'])!=0)
				$validar_data['telefono_movil'] = $_POST['telefono_movil'];
		
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_lugar($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
		
			## no se recibieron errores
			else {
			
				## ingresó comentario
				if (isset($_POST['comentario']) && strlen($_POST['comentario'])!=0)
					$validar_data['comentario'] = addslashes($_POST['comentario']);
		
				$validar_data['nombre'] = $validar_data['nombre']['value'];
			
				if ($this->Lugar->nuevo($validar_data)) {
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
	
	function editar ($id = null, $nombre = null) {
		
		$editar = false;
		
		if (isset($_POST['nombre'], $_POST['direccion'])) {
			
			$validar_data = array(
				'nombre' => array(
					'id_lugar' => $id,
					'value' => $_POST['nombre'],
					'new' => false,
					'edit' => true
				),
				'direccion' => $_POST['direccion']
			);
			
			## ingresó administrador
			if (isset($_POST['administrador']) && strlen($_POST['administrador'])!=0)
				$validar_data['administrador'] = $_POST['administrador'];
		
			## ingresó email
			if (isset($_POST['email']) && strlen($_POST['email'])!=0)
				$validar_data['email'] = $_POST['email'];
		
			## ingresó teléfono fijo
			if (isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo'])!=0)
				$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
		
			## ingresó teléfono movil
			if (isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil'])!=0)
				$validar_data['telefono_movil'] = $_POST['telefono_movil'];
		
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_lugar($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				## ingresó comentario
				if (isset($_POST['comentario']) && strlen($_POST['comentario'])!=0)
					$validar_data['comentario'] = addslashes($_POST['comentario']);
		
				$validar_data['nombre'] = $validar_data['nombre']['value'];
				
				if ($this->Lugar->editar($id, $validar_data)) {
					$editar = true;
				} else {
					$this->set('rs_editar', false);
				}
				
			} /* else */
			
		} /* envío del formulario */
		
		$search_array = array('á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', '.', '(', ')', '&', '-', '_');
		$replace_array = array('a', 'e', 'i', 'o', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'n', '', '-', '-', '', '-', '-');
		
		if (isset($id) && preg_match('/^[\d]{1,}$/', $id) && !$editar) {
			
			$data_lugar = $this->Lugar->consultar_lugar($id);
			
			## verificar que existe un lugar con el id recibido
			if (count($data_lugar)!=0) {
				
				$nombre_url = strtolower($data_lugar[0]['Lugar']['nombre']);
				$nombre_url = str_replace($search_array, $replace_array, $nombre_url);
				
				$nombre_url = preg_replace('/\s+/', '-', $nombre_url);
				$nombre_url = preg_replace('/-{2,}/', '-', $nombre_url);
				
				## validar que el nombre recibido está como debería de estar en una URL
				if (!isset($nombre) || $nombre!=$nombre_url) {
					redirectAction(strtolower($this->_controller), $this->_action, array($id, $nombre_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('id', $id);
				$this->set('nombre_url', $nombre_url);
				$this->set('data_lugar', $data_lugar);
				
				$tag_js = '
				$(function () {
					$("h2.title").append("Editar");
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
				redirectAction(strtolower($this->_controller), 'index');
			}
			
		}
		
		## se editó exitósamente
		elseif ($editar) {
			redirectAction(strtolower($this->_controller), 'ver', array($id));
		}
		
		## no se recibió id
		else {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
	}
	
	private function validar_data_lugar ($datos) {
		
		$ind_error = array();
		
		$nombre_format = '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ\.\(\)&-_]{5,60}$/';
		$direccion_format = '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ\.\(\)#\/&-_]{5,60}$/';
		$letras_format = '/^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{6,80}$/';
		$phone_format = '/^[\d]{5,20}$/';
		
		/**
		 * validar nombre del lugar
		 */
		
		## se va a crear un nuevo nombre de un lugar
		if (array_key_exists('new', $datos['nombre']) && $datos['nombre']['new']) {
			if (!preg_match($nombre_format, $datos['nombre']['value'])) {
				$ind_error['nombre'] = 'Ingrese sólo letras, números, guiones (- y _), puntos (.), ampersands (&), paréntesis y espacios.';
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
				$ind_error['nombre'] = 'Ingrese sólo letras, números, guiones (- y _), puntos (.), ampersands (&), paréntesis y espacios.';
			} else {
				$sql = 'SELECT * FROM lugares WHERE nombre = \'' . mysql_real_escape_string($datos['nombre']['value']) . '\''.
				' AND id != \'' . $datos['nombre']['id_lugar'] . '\'';
				$tmp_query = $this->Lugar->query($sql);
				if (count($tmp_query)!=0)
					$ind_error['nombre'] = 'El nombre del lugar ya se ha asignado.';
				unset($tmp_query, $sql);
			} /* else */
		} /* elseif */
		
		## validar la dirección
		if (!preg_match($direccion_format, $datos['direccion']))
			$ind_error['direccion'] = 'Ingrese sólo letras, números, guiones (- y _), puntos (.), ampersands (&), paréntesis, numerales (#), barras (/) y espacios.';
		
		## validar el nombre del administrador del lugar
		if (array_key_exists('administrador', $datos) && !preg_match($letras_format, $datos['administrador'])) 
			$ind_error['administrador'] = 'Ingrese sólo letras y espacios.';
		
		## validar email
		if (array_key_exists('email', $datos) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese una dirección de Email válida.';
		
		## validar teléfono fijo
		if(array_key_exists('telefono_fijo', $datos) && !preg_match($phone_format, $datos['telefono_fijo']))
			$ind_error['telefono_fijo'] = 'Ingrese un número de teléfono válido.';
		
		## validar teléfono movil
		if(array_key_exists('telefono_movil', $datos) && !preg_match($phone_format, $datos['telefono_movil']))
			$ind_error['telefono_movil'] = 'Ingrese un número de teléfono válido.';
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}