<?php

class PeriodosController extends VanillaController {
	
	/**
	 *
	 * @author: Jhon Adrián Cerón <jadrian.ceron@gmail.com>
	 */
	
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
		var col = "periodo";
		var orderDir = "desc";
				
		function load_dataTable (pag, sort, order) {		
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_periodos' .'";
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
		
	}
	
	function listar_periodos () {
		
		$parametros = func_get_args();
		
		$tabla = strtolower($this->_controller); ## tabla del controlador en la BD
		$campo_dft = 'periodo'; ## empieza a ordenar por este campo
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
			'periodo' => array(
				'text' => 'Período',
				'showTable' => true,
				'sort' => true,
				'where' => true
			),
			'fecha_inic' => array(
				'text' => 'Fecha <abbr title="Inicio">Inic.</abbr>',
				'showTable' => true,
				'sort' => true,
				'where' => true
			),
			'fecha_fin' => array(
				'text' => 'Fecha <abbr title="Finalización">Fin.</abbr>',
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
		$data_query = $this->Periodo->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Periodo->query($str_totalquery);
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
		if($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']) {
		
			## se recibe un id para eliminar
			if(isset($id) && preg_match('/^[\d]{1,}$/', $id)){
				$rs = $this->Periodo->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Periodo->eliminar($_POST['id']);
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
		if (isset($_POST['periodo'], $_POST['fecha_inic'], $_POST['fecha_fin'])) {
		
			$validar_data = array(
				'periodo' => array(
					'value' => $_POST['periodo'],
					'new' => true,
					'edit' => false
				),
				'fecha_inic' => $_POST['fecha_inic'],
				'fecha_fin' => $_POST['fecha_fin']
			);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_periodo($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else{
				
				$validar_data['periodo'] = $validar_data['periodo']['value'];
				
				if ($this->Periodo->nuevo($validar_data)) {
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
					
			$( "#fecha_inic, #fecha_fin" ).datepicker({
				regional: "es",
				dateFormat: "yy-mm-dd",				
				changeMonth: true,
				changeYear: true,
				showOtherMonths: true,
				selectOtherMonths: false
			});	
				
		});
		';
		
		$this->set('make_tag_js', $tag_js);
		$this->set('makejs', array('jquery.ui.datepicker-es'));
		
	}
	
	private function validar_data_periodo ($datos) {
		
		$ind_error = array();
		
		$periodo_format = '/^[\d]{4}-[\d]{1,2}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		
		## validar si el valor del periodo coincide con el patrón
		if(!preg_match($periodo_format, $datos['periodo']['value']))
			$ind_error['periodo'] = 'El formato del periodo no es válido.';
		
		## se va a crear un nuevo periodo 
		elseif (array_key_exists('new', $datos['periodo']) && $datos['periodo']['new']) {
			$tmp_query = $this->Periodo->query('SELECT * FROM periodos WHERE periodo = \'' . mysql_real_escape_string($datos['periodo']['value']) . '\'');
			if (count($tmp_query)!=0) 
				$ind_error['periodo'] = 'Existe un periodo con el valor a crear<sup>1</sup>.';
			unset($tmp_query);
		}
		
		## se va a editar un periodo
		elseif (array_key_exists('edit', $datos['periodo']) && $datos['periodo']['edit']) {
			$sql = 'SELECT * FROM periodos WHERE periodo = \'' . mysql_real_escape_string($datos['periodo']['value']) . '\'';
			$sql .= ' AND id != \'' . $datos['periodo']['id_periodo'] . '\'';
			$tmp_query = $this->Periodo->query($sql);
			if (count($tmp_query)!=0)
				$ind_error['periodo'] = 'Existe un periodo con el valor a crear<sup>1</sup>.';
			unset($sql, $tmp_query);
		}
		
		## validar que la fecha de inicio coincida con el patrón de fecha
		if(!preg_match($fecha_format, $datos['fecha_inic']))
			$ind_error['fecha_inic'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## validar que la fecha de finalización coincida con el patrón de fecha
		if(!preg_match($fecha_format, $datos['fecha_fin']))
			$ind_error['fecha_fin'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## ambas fechas coinciden con el patrón
		if (preg_match($fecha_format, $datos['fecha_inic']) && preg_match($fecha_format, $datos['fecha_fin'])) {
			## la fecha de inicio debe ser menor que la fecha fin
			if (strtotime($datos['fecha_inic']) < strtotime($datos['fecha_fin'])) {
				## validar que no se crucen las fechas con las de otro periodo
				$sql = 'SELECT * FROM periodos WHERE (';
				$sql .= '(\'' . $datos['fecha_inic'] . '\' BETWEEN fecha_inic AND fecha_fin) OR';
				$sql .= '(\'' . $datos['fecha_fin'] . '\'  BETWEEN fecha_inic AND fecha_fin) OR';
				$sql .= '(fecha_inic BETWEEN \'' . $datos['fecha_inic'] . '\' AND \'' . $datos['fecha_fin'] . '\')';
				$sql .= ')';
				## si se está editan, excluir de la validación a la fechas actuales
				if (array_key_exists('edit', $datos['periodo']) && $datos['periodo']['edit'])
					$sql .= ' AND id != \'' . $datos['periodo']['id_periodo'] . '\'';
				$tmp_query = $this->Periodo->query($sql);
				if (count($tmp_query)!=0)
					$ind_error['fecha_inic'] = 'Las fechas del período a crear, se cruzan con las del periodo ' . $tmp_query[0]['Periodo']['periodo'] . '.';
				unset($sql, $tmp_query);
			} else {
				$ind_error['fecha_inic'] = 'La fecha inicio debe ser menor que la fecha de finalización.';
			}			
		}
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}