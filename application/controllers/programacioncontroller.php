<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * 
 * La tabla (en la BD) de este controlador se llama 'cursos' ...
 * @author Jhon Adrián Cerón
 *
 */

class ProgramacionController extends VanillaController {
	
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
		var col = "periodo.periodo";
		var orderDir = "desc";
		
		function load_dataTable (pag, sort, order) {
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_programaciones' .'";
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
	
	function listar_programaciones () {
		
		$parametros = func_get_args();
		
		/**
		 * 
		 * empezar a ordenar por este campo ...
		 * 	<alias>tabla.campo
		 * @var string
		 */
		$campo_dft = 'periodo.periodo';
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
				'regex' => '/^[a-zA-Z 0-9-]{1,45}$/'
			)
		);
		
		$campos_tabla = array(
			'periodos' => array(
				'table' => true,
				'alias' => 'periodo',
				'fields' => array(
					'periodo' => array(
						'text' => 'Periodo',
						'showTable' => true,
						'sort' => true,
						'where' => true
					) /* end periodo */
				) /* end fields */
			), /* end periodos */
			'actividades' => array(
				'table' => true, ## es una tabla el nodo padre de este árbol
				'alias' => 'actividad',
				'fields' => array(
					'nombre' => array(
						'text' => 'Actividad',
						'showTable' => true, ## mostrar como columna en la tabla
						'sort' => true, ## puede ordenarse la tabla por este campo
						'where' => true ## buscar por esta columna
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
			'cursos' => array(
				'table' => true,
				'alias' => 'curso',
				'fields' => array(
					'id' => array(
						'showTable' => false,
						'sort' => false,
						'where' => false
					), /* end id */
					'monitor_dni' => array(
						'text' => 'Monitor',
						'showTable' => true,
						'sort' => false,
						'where' => true
					), /* end monitor_dni */
					'abierto' => array(
						'text' => 'Abierto',
						'showTable' => true,
						'sort' => true,
						'where' => true
					) /* end abierto */
				) /* end fields */
			), /* end cursos */
			## realizar los joins entre las tablas
			'join' => array(
				0 => 'curso.periodo_id = periodo.id',
				1 => 'curso.actividad_id = actividad.id',
				2 => 'actividad.area_id = area.id'
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
		$data_query = $this->Programacion->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Programacion->query($str_totalquery); 
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
	
	function ver ($id = null, $actividad = null) {
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '&', '_');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N', '', '' );

		## se recibe un id de una programación
		if (isset($id) && preg_match('/^[\d]{1,}$/', $id)) {
			
			$data_programacion = $this->Programacion->consultar_programacion($id);
			
			## la programación existe
			if (count($data_programacion)!=0) {
				
				$actividad_url = strtolower($data_programacion[0]['Actividad']['nombre']);
				$actividad_url = str_replace($search_caract_espec, $replace_caract_espec, $actividad_url); ## reemplazo de caracteres
				$actividad_url = preg_replace('/\s+/', '-', $actividad_url); ## reemplazar espacios por guiones
				$actividad_url = preg_replace('/-{2,}/', '-', $actividad_url); ## reemplazar dos o más guiones seguidos, por uno solo
				
				## NO se recibe el nombre de la actividad o NO está como debería aparecer en la URL
				if (!isset($actividad) || $actividad!=$actividad_url) {
					redirectAction(strtolower($this->_controller), 'ver', array($id, $actividad_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propio de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('id', $id);
				$this->set('actividad_url', $actividad_url);
				$this->set('data_programacion', $data_programacion);
				$this->set('total_inscritos', $this->Programacion->total_inscritos($id));
				
				$tag_js = '
				
				var info_preload = \'<div id="info_preload" class="dataTablas_preload">Cargando...</div>\';
				
				function load_dataTable (controlador, pag, record, sort, order, search) {
					
					$(function() {
						
						$( "#dynamic-" + controlador ).html( info_preload );
						var url = "'. BASE_PATH . '/" + controlador + "/listar_" + controlador + "/' . $id .'";
						if(pag.length!=0) url += "/pag=" + pag;
						if(record.length!=0) url += "/record=" + record;
						if(sort.length!=0) url += "/sort=" + sort;
						if(order.length!=0) url += "/order=" + order;
						if(search.length!=0) url += "/q=" + encodeURIComponent(search);
						$.ajax({
							url: url,
							success: function(data) {
								$( "#dynamic-" + controlador ).html(data);
							}
						});
						
					});
					
				}
				
				function closeDialog(dialog, id_msj, div){
					
					$(function() {
						$("#dialog-" + dialog).dialog("close");  
						return false; 						
					});
					
					customDialog(id_msj, div);
						
				}
				
				function customMensaje (id_msj, div) {
				
					var mensajes = new Array();
					mensajes[0] = "Vaya! No tienes el permiso necesario para interactuar con la página solicitada.";
					mensajes[1] = "Existe un error al cargar la página solicitada.";	

					var msj_dialog = "<div class=\"message notice\"><p>" + mensajes[id_msj] + "</p></div>"; 
					
					$(function() {
						$( "#showMensaje-" + div ).html(msj_dialog);
						$( "#showMensaje-" + div ).fadeIn("slow");
						$(".flash").click(function() {$(this).fadeOut("slow", function() { $(this).css("display", "none"); });});
					});
					
					return false;
				
				}
				
				$(document).ready(function() {
				
					load_dataTable("horarios", 1, ' . PAGINATE_LIMIT . ', "", "", "");
					load_dataTable("inscripciones", 1, ' . PAGINATE_LIMIT . ', "", "", "");
				
					$( "h2.title" ).append("Ver");
					
					$( "#tabs" ).tabs({
						selected: 1
					});
					
					$( "#dialog-nuevo-inscripcion" ).dialog({
						modal: true,
						autoOpen: false,
						resizable: false,
						height: 300,
        				width: 650,
        				open: function() {
        					$("#dialog-nuevo-inscripcion").load("' . BASE_PATH . '/' . 'inscripciones' . '/' . 'nuevo' . '/' . $id . '");
        				},
        				close: function () {
        					load_dataTable(\'inscripciones\', 1, ' . PAGINATE_LIMIT . ', "", "", "");
        				},
        				buttons: {
        					"Guardar": function () {
        						$( "#formulario-inscripcion" ).submit();
        					},
        					"Cancelar": function () {
        						$( this ).dialog( "close" );
        					}
        				}
					});
					
					$( "#btn_nuevo_inscripcion" ).click(function() {
							$( "#dialog-nuevo-inscripcion" ).dialog( "open" );
					});
					
				});
				
				';
				
				$this->set('make_tag_js', $tag_js);
				
				$this->set('makecss', array('jquery.qtip.min'));
				$this->set('makejs', array('jquery.qtip.min'));
				
			} else {
				redirectAction(strtolower($this->_controller), 'index');
			}
			
		} else {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
	}
	
	function eliminar ($id = null) {
		
		## el usuario tiene permiso para eliminar
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']) {
		
			## se recibe un id para eliminar
			if(isset($id) && preg_match('/^[\d]{1,}$/', $id)){
				$rs = $this->Programacion->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Programacion->eliminar($_POST['id']);
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
	
	function consultar_programacion_fk ($id) {
		return $this->Programacion->consultar_programacion($id);
	}
	
	function nuevo () {
		
		if (isset($_POST['actividad'], $_POST['periodo'])) {
			
			$validar_data = array(
				'actividad' => $_POST['actividad'],
				'periodo' => $_POST['periodo']
			);
			
			## ingresó monitor
			if (isset($_POST['monitor']) && strlen($_POST['monitor'])!=0)
				$validar_data['monitor'] = $_POST['monitor'];
			
			## ingresó fecha de inicio
			if (isset($_POST['fecha_inic']) && strlen($_POST['fecha_inic'])!=0)
				$validar_data['fecha_inic'] = $_POST['fecha_inic'];
			
			## ingresó fecha de finalización
			if (isset($_POST['fecha_fin']) && strlen($_POST['fecha_fin'])!=0)
				$validar_data['fecha_fin'] = $_POST['fecha_fin'];
			
			## la programación es abierta
			if (isset($_POST['abierto'])) {	
				$validar_data['abierto'] = 1;
			} else {
				$validar_data['abierto'] = 0;
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_programacion($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				## ingresó comentario
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
			
		} /* envío del formulario */
		
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
	
	/**
	 * 
	 * Editar la programación de una actividad ...
	 * @param int $id
	 * @param string $actividad
	 */
	function editar ($id = null, $actividad = null) {
		
		$editar = false;
		
		## se envió el formulario
		if (isset($_POST['actividad'], $_POST['periodo'])) {
			
			$validar_data = array(
				'actividad' => $_POST['actividad'],
				'periodo' => $_POST['periodo']
			);
			
			## ingresó monitor
			if (isset($_POST['monitor']) && strlen($_POST['monitor'])!=0)
				$validar_data['monitor'] = $_POST['monitor'];
			
			## ingresó fecha de inicio
			if (isset($_POST['fecha_inic']) && strlen($_POST['fecha_inic'])!=0)
				$validar_data['fecha_inic'] = $_POST['fecha_inic'];
			
			## ingresó fecha de finalización
			if (isset($_POST['fecha_fin']) && strlen($_POST['fecha_fin'])!=0)
				$validar_data['fecha_fin'] = $_POST['fecha_fin'];
			
			## la programación es abierta
			if (isset($_POST['abierto'])) {	
				$validar_data['abierto'] = 1;
			} else {
				$validar_data['abierto'] = 0;
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_programacion($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				## ingresó comentario
				if(strlen($_POST['comentario'])!=0) { 
					$validar_data['comentario'] = addslashes($_POST['comentario']);
				} else {
					$validar_data['comentario'] = '';
				}
			
				$validar_data['actividad_id'] = $validar_data['actividad'];
				$validar_data['periodo_id'] = $validar_data['periodo'];
				unset($validar_data['actividad'], $validar_data['periodo']);
				
				## ingresó monitor
				if (array_key_exists('monitor', $validar_data)) {
					$validar_data['monitor_dni'] = $validar_data['monitor'];
					unset($validar_data['monitor']);
				} else {
					$validar_data['monitor_dni'] = '';
				}
				
				## NO ingresó fecha de inicio
				if (!array_key_exists('fecha_inic', $validar_data))
					$validar_data['fecha_inic'] = ''; 
				
				## NO ingresó fecha de finalización
				if (!array_key_exists('fecha_fin', $validar_data))
					$validar_data['fecha_fin'] = '';

				if($this->Programacion->editar($id, $validar_data)){
					$editar = true;
				} else {
					$this->set('rs_editar', false);
				}
				
			} /* else */
			
		} /* envío del formulario */
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ', '&', '_');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N', '', '' );
		
		## se recibe un id de una programación
		if (isset($id) && preg_match('/^[\d]{1,}$/', $id) && !$editar) {
			
			$data_programacion = $this->Programacion->consultar_programacion($id);
			
			## la programación existe
			if (count($data_programacion)!=0) {
				
				$actividad_url = strtolower($data_programacion[0]['Actividad']['nombre']);
				$actividad_url = str_replace($search_caract_espec, $replace_caract_espec, $actividad_url); ## reemplazo de caracteres
				$actividad_url = preg_replace('/\s+/', '-', $actividad_url); ## reemplazar espacios por guiones
				$actividad_url = preg_replace('/-{2,}/', '-', $actividad_url); ## reemplazar dos o más guiones seguidos, por uno solo
				
				## NO se recibe el nombre de la actividad o NO está como debería aparecer en la URL
				if (!isset($actividad) || $actividad!=$actividad_url) {
					redirectAction(strtolower($this->_controller), 'editar', array($id, $actividad_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propio de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('id', $id);
				$this->set('actividad_url', $actividad_url);
				$this->set('data_programacion', $data_programacion);
				
				$lista_periodos = performAction('periodos', 'listar_periodos_group_fk', array());
				$this->set('lista_periodos', $lista_periodos);
				
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
					
					$("h2.title").append( "Programación -> Editar" );
		
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				$this->set('makejs', array('jquery.ui.datepicker-es', 'jquery.textareaCounter.plugin'));
				
			} else {
				redirectAction(strtolower($this->_controller), 'index');
			} /* else */
			
		}
		
		## editó exitósamente
		elseif ($editar) {
			redirectAction(strtolower($this->_controller), 'ver', array($id));
		}
		
		## no se recibieron datos
		else {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
	}
	
	private function validar_data_programacion ($datos) {
		
		$ind_error = array();
		
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$dni_format = '/^[\d]{5,20}$/';
		$select_format = '/^[\d]{1,}$/';
		
		## validar la selección de una actividad
		if (!preg_match($select_format, $datos['actividad']))
			$ind_error['actividad'] = 'Seleccione una actividad.';
		
		## validar la selección de un periodo
		if (!preg_match($select_format, $datos['periodo']))
			$ind_error['periodo'] = 'Seleccione un periodo.';
		
		## validar monitor
		if (array_key_exists('monitor', $datos)) {
			## validar que se haya ingresado un número de identificación válido
			if (!preg_match($dni_format, $datos['monitor'])) {
				$ind_error['monitor'] = 'Ingrese un número de identificación válido';
			} else {
				## validar que el número de identifación exista en la BD
				$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['monitor']));
				if (count($data_persona)==0) {
					$ind_error['monitor'] = 'El número de identificación ingresado no corresponde a una persona del sistema.';
				} else {
					## validar que la persona esté activa
					if ($data_persona[0]['Persona']['estado']!=1) {
						$ind_error['monitor'] = 'El número de identificación corresponde a una persona que no está activa.';
					} else {
						## validar que la persona sea monitor
						if ($data_persona[0]['Persona']['monitor']!=1)
							$ind_error['monitor'] = 'El número de identificación corresponde a una persona que no es monitor.';
					} /* else */
				} /* else */
				unset ($data_persona);
			}
		} /* validación del monitor */
		
		## validar fecha de inicio de la programación
		if (array_key_exists('fecha_inic', $datos) && !preg_match($fecha_format, $datos['fecha_inic']))
			$ind_error['fecha_inic'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## validar fecha de finalización
		if (array_key_exists('fecha_fin', $datos) && !preg_match($fecha_format, $datos['fecha_fin']))
			$ind_error['fecha_fin'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## se ingresaron fechas y éstas son correctas
		if (array_key_exists('fecha_inic', $datos) && array_key_exists('fecha_fin', $datos) && preg_match($fecha_format, $datos['fecha_inic']) && preg_match($fecha_format, $datos['fecha_fin'])) {
			## validar que la fecha inicial sea menor que la fecha final
			if (strtotime($datos['fecha_inic']) >= strtotime($datos['fecha_fin'])) {
				$ind_error['fecha_inic'] = 'La fecha inicial debe ser menor que la fecha de finalización.';
			} else {
				## verificar que las fechas pertenezcan a un determinado periodo
				$sql_temp = 'SELECT * FROM periodos WHERE (';
				$sql_temp .= '(\'' . $datos['fecha_inic'] . '\' BETWEEN fecha_inic AND fecha_fin) AND'; 
				$sql_temp .= '(\'' . $datos['fecha_fin'] . '\' BETWEEN fecha_inic AND fecha_fin)';
				$sql_temp .= ')'; 
				$tmp_query = $this->Programacion->query($sql_temp);
				## las fechas ingresadas no pertenecen a ningún periodo
				if (count($tmp_query)==0)
					$ind_error['fecha_inic'] = 'Las fechas de la programación deben de pertenecer a un determinado periodo.';
				unset($sql_temp, $tmp_query);
			} /* else */
		}
		
		return $ind_error;
		
	}
	
	function afterAction () {
		
	}
	
}