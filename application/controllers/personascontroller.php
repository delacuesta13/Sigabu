<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class PersonasController extends VanillaController{
	
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
	
	function index ($tipo_mensaje = null, $str_mensaje = null) {
		
		$tag_js = '
		
		var info_preload = \'<div id="info_preload" class="dataTablas_preload">Cargando...</div>\';
		var col = "nombres";
		var orderDir = "asc";
		
		function load_dataTable (pag, sort, order) {		
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_personas' .'";
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
		
		/*******************************************************/
		
		if(isset($tipo_mensaje, $str_mensaje) && strtolower($tipo_mensaje)=='query' && preg_match('/^[\d]{1,}-[\d]{1,}$/', $str_mensaje)){
			$str_mensaje = explode('-', $str_mensaje);
			$this->set('showMensaje', 'Se ha ejecutado exitósamente ' . $str_mensaje[0] . ' petición (es), de ' . $str_mensaje[1] . ' solicitada (s).');
		}
				
	}
	
	function listar_personas (){			
		
		$parametros = func_get_args();
		
		$tabla = strtolower($this->_controller); ## tabla del controlador en la BD
		$campo_dft = 'nombres'; ## empieza a ordenar por este campo
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
			'tipo_dni' => array(
				'text' => 'Tipo de <abbr title="Identificación">Ident.</abbr>',
				'sort' => true, ## puede ordenarse la tabla por este campo
				'where' => false ## buscar por esta columna
			),
			'dni' => array(
				'text' => 'Identificación',
				'sort' => false,
				'where' => true
			),
			'nombres' => array(
				'text' => 'Nombres',
				'sort' => true,
				'where' => true
			),
			'apellidos' => array(
				'text' => 'Apellidos',
				'sort' => true,
				'where' => true
			),
			'fecha_nac' => array(
				'text' => 'Fecha de <abbr title="Nacimiento">Nac.</abbr>',
				'sort' => true,
				'where' => true
			),
			'genero' => array(
				'text' => 'Género',
				'sort' => true,
				'where' => false
			),
			'monitor' => array(
				'text' => 'Monitor',
				'sort' => true,
				'where' => false
			),
			'estado' => array(
				'text' => 'Estado',
				'sort' => true,
				'where' => false
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
		
		/*
		 * inicializo el query de consulta
		 */
		$str_query = 'SELECT SQL_CALC_FOUND_ROWS ';
		
		/**
		 * agrego las columnas al query
		 */		
		foreach($campos_tabla as $campos => $def){
			$str_query .= $campos . ', ';
		}
		$str_query = rtrim($str_query, ', ') . ' ';		
		
		unset($campos, $def);
		
		$str_query .= 'FROM ' . $tabla . ' ';
		
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
		
		/*
		 * agrego la columna y la dirección del ordenamiento
		 */
		if(array_key_exists('sort', $opciones_data) && array_key_exists('order', $opciones_data) && $campos_tabla[strtolower(mysql_real_escape_string($opciones_data['sort']))]['sort']){
			$str_query .= ' ORDER BY ' . mysql_real_escape_string($opciones_data['sort']) . ' ' . strtoupper(mysql_real_escape_string($opciones_data['order']));
		} else {
			$str_query .= ' ORDER BY ' . $campo_dft . ' ' . $dir_dft;
		}
		
		/*
		 * agrego el limit
		 */
		if (!array_key_exists('pag', $opciones_data)) $opciones_data['pag'] = $pag_dft;
		if (!array_key_exists('record', $opciones_data)) $opciones_data['record'] = $record_dft;
		$offset = $opciones_data['record'] * ($opciones_data['pag'] - 1);		
		$str_query .= ' LIMIT '. $offset . ', ' . $opciones_data['record'];		 
		
		## ejecuto la consulta y recibo las tuplas
		$data_query = $this->Persona->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Persona->query($str_totalquery); 
		$totalreg_query = $totalreg_query[0]['']['total']; 
		
		$offset++;
		$limit = ($opciones_data['pag'] * $opciones_data['record']);
		
		/**
		 * envío variables a la vista
		 */		
		$this->set('campos_tabla', $campos_tabla);
		$this->set('data_query', $data_query);
		$this->set('totalreg_query', $totalreg_query);
		$this->set('pagina', $opciones_data['pag']);
		$this->set('limit', $limit);
		$this->set('record', $opciones_data['record']);
		
		if (array_key_exists('sort', $opciones_data) && array_key_exists('order', $opciones_data)) {
			$this->set('sort', $opciones_data['sort']);
			$this->set('order', $opciones_data['order']);
		} else {
			$this->set('sort', $campo_dft);
			$this->set('order', $dir_dft);
		}
		
		unset ($data_query, $totalreg_query, $offset, $limit);
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;

		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	/**
	 * 
	 * eliminar personas ...
	 * @param int $dni
	 */
	function eliminar ($dni =  null) {
		
		## el usuario tiene permiso para eliminar
		if($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']){
		
			## se recibe un dni para eliminar
			if(isset($dni) && preg_match('/^[\d]{5,20}$/', $dni)){
				$rs = $this->Persona->eliminar(array($dni));
				redirectAction('personas', 'index', array('query', $rs['trueQuery']. '-' . $rs['totalQuery']));
			}
			## se recibe (n) mediante post, dni (s) para eliminar
			elseif (isset($_POST['dni']) && is_array($_POST['dni']) && count($_POST['dni'])!=0) {
				$rs = $this->Persona->eliminar($_POST['dni']);
				redirectAction('personas', 'index', array('query', $rs['trueQuery']. '-' . $rs['totalQuery']));
			}
			## no se recibe nada
			else{
				redirectAction(strtolower($this->_controller), 'index');
			}
		
		} else {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '1'));
		}	
		
	}
	
	/**
	 * 
	 * action para crear nuevas personas ...
	 */
	function nuevo () {

		## se ha enviado el formulario
		if (isset($_POST['nombres'], $_POST['apellidos'], $_POST['tipo_dni'], $_POST['dni'], $_POST['genero'])) {
			
			$validar_data = array(
				"nombres" => $_POST['nombres'],
				"apellidos" => $_POST['apellidos'],
				"tipo_dni" => $_POST['tipo_dni'],
				"dni" => array(
							"value" => $_POST['dni'],
							"new" => true ## verdadero si se va a crear una nueva persona
						),
				"genero" => $_POST['genero']
			);
			
			## ingresó el teléfono fijo
			if(isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo'])!=0)
				$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
			
			## ingresó el teléfono móvil
			if(isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil'])!=0)
				$validar_data['telefono_movil'] = $_POST['telefono_movil'];
			
			## ingresó email
			if(isset($_POST['email']) && strlen($_POST['email'])!=0)
				$validar_data['email'] = $_POST['email'];
			
			## ingresó fecha de nacimiento
			if(isset($_POST['fecha_nac']) && strlen($_POST['fecha_nac'])!=0)
				$validar_data['fecha_nac'] = $_POST['fecha_nac'];
			
			## ingresó dirección de residencia
			if(isset($_POST['direccion_residencia']) && strlen($_POST['direccion_residencia'])!=0)
				$validar_data['direccion_residencia'] = $_POST['direccion_residencia'];
			
			## activó a la persona como monitor
			if(isset($_POST['monitor'])){
				$validar_data['monitor'] = 1;
			} else {
				$validar_data['monitor'] = 0;
			}
			
			## estado activo para la persona
			if(isset($_POST['estado'])){
				$validar_data['estado'] = 1;
			} else {
				$validar_data['estado'] = 0;
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_persona($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);			
			
			## no se recibieron errores
			else{
				
				## limpio la dirección de residencia, si se declaró, evitando sql injection
				if(array_key_exists('direccion_residencia', $validar_data))	
					$validar_data = addslashes($validar_data['direccion_residencia']);
			
				$validar_data['dni'] = $validar_data['dni']['value'];
				
				if ($this->Persona->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
				
			}/* else */
		
		} /* envío del formulario */
		
		$tag_js = '
		$(function() {
			
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			}); 
			
			$( "#fecha_nac" ).datepicker({
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
	
	/**
	 *
	 * Consultar una persona por su DNI ...
	 * @param int $dni
	 */
	function consultar_persona_fk ($dni) {
		if(preg_match('/^[\d]{5,20}$/', $dni))
			return $this->Persona->consultar_persona($dni);
		else
			return 0;
	}
	
	function ver ($dni = null, $nombre = null) {
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N');
		
		## se recibe el dni, y éste coincide con el patrón
		if(isset($dni) && preg_match('/^[\d]{5,20}$/', $dni)) {
		
			## busco la persona por su dni
			$data_persona = $this->consultar_persona_fk($dni);
		
			## la persona existe
			if (count($data_persona)!=0) {
			
				/**
				 *
				 * el nombre (junto con el apellido) como debe de estar en la url ...
				 * @var string
				 */
				$nombre_url = $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'];
				$nombre_url = str_replace($search_caract_espec, $replace_caract_espec, $nombre_url);
				## reemplazo los espacios por guiones
				$nombre_url = preg_replace('/\s+/', '-', $nombre_url);
				## reemplazo dos o más guiones seguidos por uno solo
				$nombre_url = preg_replace('/-{2,}/', '-', $nombre_url);
				## a minúsculas toda la cadena
				$nombre_url = strtolower($nombre_url);
		
				/**
				 * si no se recibe el nombre, o el nombre es diferente a
				 * como debería de ser para la persona, redirecciono a
				 * esta 'action' con el nombre como debería de ser.
				 */
				if($nombre_url!=$nombre){
					redirectAction(strtolower($this->_controller), $this->_action, array($dni, $nombre_url));
				}
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propia de la 'action' ***************************
				 *******************************************************************************************/
				
				$this->set('dni', $dni);
				$this->set('nombre_url', $nombre_url);
				$this->set('data_persona', $data_persona);
				
				$tag_js = '
				
				var info_preload = \'<div id="info_preload" class="dataTablas_preload">Cargando...</div>\';
				var col = "periodo.periodo";
				var orderDir = "desc";
				
				function load_dataTable (pag, record, sort, order, search) {
					$(function() {
						$( "#dynamic" ).html( info_preload );
						var url = "'. BASE_PATH . '/'. 'perfiles' . '/' . 'listar_perfiles' . '/' . $dni .'";
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
				
				function closeDialog(id_msj){
					$(function() {
						$("#dialog-nuevo").dialog("close");  
						return false; 						
					});
					customDialog(id_msj);	
				}
				
				function showPerfil (id, dni) {
					$(function() {
						$( "#dialog-show-perfil" ).dialog({
							modal: true,
							autoOpen: true, 
							resizable: false,
							height: 430,
        					width: 520,
        					open: function() {
        						$("#dialog-show-perfil").load("' . BASE_PATH . '/' . 'perfiles' . '/' . 'ver' . '/" + id + "/" + dni);
        					},
        					buttons: {
        						"Cerrar": function () {
        							$( this ).dialog( "close" );
        						}
        					}
						});
					});
				}
				
				function closeDialog_2(id_msj){
					$(function() {
						$("#dialog-show-perfil").dialog("close");  
						return false; 						
					});
					customDialog(id_msj);	
				}
				
				function editPerfil (id, dni) {
					$(function() {
						$( "#dialog-edit-perfil" ).dialog({
							modal: true,
							autoOpen: true, 
							resizable: false,
							height: 480,
        					width: 650,
        					open: function() {
        						$("#dialog-edit-perfil").load("' . BASE_PATH . '/' . 'perfiles' . '/' . 'editar' . '/" + id + "/" + dni);
        					},
        					close: function () {
        						load_dataTable(1, ' . PAGINATE_LIMIT . ', col, orderDir, \'\');
        					},
        					buttons: {
        						"Guardar": function () {
        							$( "#formulario_editar" ).submit();
        						},
        						"Cancelar": function () {
        							$( this ).dialog( "close" );
        						}
        					}
						});
					});
				}
				
				function closeDialog_3(id_msj){
					$(function() {
						$("#dialog-edit-perfil").dialog("close");  
						return false; 						
					});
					customDialog(id_msj);	
				}
				
				function customDialog(id_msj){
					
					var mensajes = new Array();
					mensajes[0] = "La persona a la cual se asignará el perfil, parece no existir.";					
					mensajes[1] = "La persona a la cual se asignará el perfil, debe de estar <i>activa</i>.";					
					mensajes[2] = "Vaya! No tienes el permiso necesario para interactuar con la página solicitada.";					
					mensajes[3] = "Existe un error al cargar los datos del perfil solicitado.";					
					mensajes[4] = "Se ha editado exitósamente el perfil.";					
					
					var msj_dialog = "<div class=\"message notice\"><p>" + mensajes[id_msj] + "</p></div>"; 					
					
					$(function() {
						$( "#showMensaje" ).html(msj_dialog);
						$( "#showMensaje" ).fadeIn("slow");
						$(".flash").click(function() {$(this).fadeOut("slow", function() { $(this).css("display", "none"); });});
					});
					
					return false;					
				}
				
				$(document).ready(function() {
				
					load_dataTable(1, ' . PAGINATE_LIMIT . ', col, orderDir, \'\');	
											
					$( "h2.title" ).append("Ver");
					$( "#tabs" ).tabs({
						selected: 0
					});
					
					$( "#dialog-nuevo" ).dialog({
						modal: true,
						autoOpen: false,
						resizable: false,
						height: 480,
        				width: 650,
        				open: function() {
        					$("#dialog-nuevo").load("' . BASE_PATH . '/' . 'perfiles' . '/' . 'nuevo' . '/' . $dni . '");
        				},
        				close: function () {
        					load_dataTable(1, ' . PAGINATE_LIMIT . ', col, orderDir, \'\');
        				},
        				buttons: {
        					"Guardar": function () {
        						$( "#formulario" ).submit();
        					},
        					"Cancelar": function () {
        						$( this ).dialog( "close" );
        					}
        				}
					});
					
					$( "#btn_nuevo" ).click(function() {
							$( "#dialog-nuevo" ).dialog( "open" );
					});
										
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
	
	function editar ($dni = null, $nombre = null) {
		
		$editar = false;
		## redireccionar a ver este persona, con su nuevo dni
		$edit_dni = 0;
		
		## se ha enviado el formulario
		if (isset($_POST['nombres'], $_POST['apellidos'], $_POST['tipo_dni'], $_POST['dni'], $_POST['genero'])) {
		
			$validar_data = array(
				"nombres" => $_POST['nombres'],
				"apellidos" => $_POST['apellidos'],
				"tipo_dni" => $_POST['tipo_dni'],
				"dni" => array(
					"value" => $_POST['dni'],
					"new" => false, ## verdadero si se va a crear una nueva persona
					'last_value' => $dni,
					"edit" => true
				),
				"genero" => $_POST['genero']
			);
			
			## ingresó el teléfono fijo
			if(isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo'])!=0)
				$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
				
			## ingresó el teléfono móvil
			if(isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil'])!=0)
				$validar_data['telefono_movil'] = $_POST['telefono_movil'];
						
			## ingresó email
			if(isset($_POST['email']) && strlen($_POST['email'])!=0)
				$validar_data['email'] = $_POST['email'];
				
			## ingresó fecha de nacimiento
			if(isset($_POST['fecha_nac']) && strlen($_POST['fecha_nac'])!=0)
				$validar_data['fecha_nac'] = $_POST['fecha_nac'];
						
			## ingresó dirección de residencia
			if(isset($_POST['direccion_residencia']) && strlen($_POST['direccion_residencia'])!=0)
				$validar_data['direccion_residencia'] = $_POST['direccion_residencia'];
						
			## activó a la persona como monitor
			if(isset($_POST['monitor'])){
				$validar_data['monitor'] = 1;
			} else {
				$validar_data['monitor'] = 0;
			}
				
			## estado activo para la persona
			if(isset($_POST['estado'])){
				$validar_data['estado'] = 1;
			} else {
				$validar_data['estado'] = 0;
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_persona($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else{				
				## limpio la dirección de residencia, si se declaró, evitando sql injection
				if(array_key_exists('direccion_residencia', $validar_data))
					$validar_data = addslashes($validar_data['direccion_residencia']);
					
				$validar_data['dni'] = $validar_data['dni']['value'];
				
				if($this->Persona->editar($dni, $validar_data)){
					$editar = true;
					$edit_dni = $validar_data['dni'];
				} else {
					$this->set('rs_editar', false);
				}			
			} /* else */
		
		} /* if envío de formulario */
		
		$search_caract_espec = array('á', 'Á', 'é', 'É', 'í', 'Í', 'ó', 'Ó', 'ú', 'Ú', 'ñ', 'Ñ');
		$replace_caract_espec = array('a', 'A', 'e', 'E', 'i', 'I', 'o', 'O', 'u', 'U', 'n', 'N');
		
		## se recibe el dni, y éste coincide con el patrón
		if(isset($dni) && preg_match('/^[\d]{5,20}$/', $dni) && !$editar) {
		
			## busco la persona por su dni
			$data_persona = $this->consultar_persona_fk($dni);
	
			## la persona existe
			if (count($data_persona)!=0) {
			
				/**
				 * 
				 * el nombre (junto con el apellido) como debe de estar en la url ...
				 * @var string
				 */			
				$nombre_url = $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'];
				$nombre_url = str_replace($search_caract_espec, $replace_caract_espec, $nombre_url);
				## reemplazo los espacios por guiones
				$nombre_url = preg_replace('/\s+/', '-', $nombre_url);
				## reemplazo dos o más guiones seguidos por uno solo
				$nombre_url = preg_replace('/-{2,}/', '-', $nombre_url);
				## a minúsculas toda la cadena
				$nombre_url = strtolower($nombre_url);
				
				/**
				 * si no se recibe el nombre, o el nombre es diferente a
				 * como debería de ser para la persona, redirecciono a 
				 * esta 'action' con el nombre como debería de ser.
				 */
				if($nombre_url!=$nombre){
					redirectAction(strtolower($this->_controller), $this->_action, array($dni, $nombre_url));
				}
				
				/*******************************************************************************************
				 *************** Ya aquí empieza el código propia de la 'action' *************************** 
				 *******************************************************************************************/
				
				$this->set('dni', $dni);
				$this->set('nombre_url', $nombre_url);
				$this->set('data_persona', $data_persona);
				
				$tag_js = '
				$(function() {
							
					$( "#fecha_nac" ).datepicker({
						regional: "es",
						dateFormat: "yy-mm-dd",				
						changeMonth: true,
						changeYear: true,
						showOtherMonths: true,
						selectOtherMonths: false
					});	
						
					$( "h2.title" ).append("Editar");
						
				});
				';
				
				$this->set('make_tag_js', $tag_js);
				$this->set('makejs', array('jquery.ui.datepicker-es'));
				
				
			} else {
				redirectAction(strtolower($this->_controller), 'index');
			}
		
		} 
		
		## se editó exitósamente
		elseif($editar){
			redirectAction(strtolower($this->_controller), 'ver', array($edit_dni));
		}
		
		## no se recibió $dni, o no era válido éste
		else {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
	}
		
	/**
	 * 
	 * Validar los datos de las personas ...
	 * @param array $datos
	 */
	private function validar_data_persona($datos){
		
		$ind_error = array();
		
		$letras_format = '/^[a-zA-Z áéíóúñÁÉÍÓÚÑ]{3,45}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$phone_format = '/^[\d]{5,20}$/';
		$tipos_dni = array("cc", "ce", "ti", "rc");
		$generos = array("h", "m");
		$direccion_format = '/^[a-zA-Z0-9 áéíóúñÁÉÍÓÚÑ\.\(\)#\/&-_]{5,60}$/';
		
		## validar nombres
		if(array_key_exists('nombres', $datos) && !preg_match($letras_format, $datos['nombres']))
			$ind_error['nombres'] = 'Ingrese sólo letras y espacios.';
		
		## validar apellidos
		if(array_key_exists('apellidos', $datos) && !preg_match($letras_format, $datos['apellidos']))
			$ind_error['apellidos'] = 'Ingrese sólo letras y espacios.';
		
		/*
		 * dni es un array, y si el key (dentro de éste) 'new' es true
		 * se valida tipo_dni y dni, porque se va a crear una nueva persona.
		 */
		if(array_key_exists('dni', $datos) && is_array($datos['dni']) && $datos['dni']['new']){			
			## validar que se haya seleccionado un tipo de identificación
			if(!in_array(strtolower($datos['tipo_dni']), $tipos_dni))
				$ind_error['tipo_dni'] = 'Seleccione el Tipo de Identificación.';
		
			## validar identificación
			if(!preg_match($phone_format, $datos['dni']['value']))
				$ind_error['dni'] = 'Ingrese el número de Identificación.';
			
			## el número de identificación es válido, verificar que no exista ya el número en la BD
			else{
				$verif_dni = $this->consultar_persona_fk($datos['dni']['value']);
				## existe una persona con mismo dni
				if(!is_array($verif_dni) || count($verif_dni) != 0)
					$ind_error['dni'] = 'El número de Identificación ya se ha asignado a otra persona.';
				unset($verif_dni);
			}			
		} /* end dni */
		################
		## editar dni ##
		################
		elseif (array_key_exists('dni', $datos) && is_array($datos['dni']) && !$datos['dni']['new'] && $datos['dni']['edit']) {
			## validar que se haya seleccionado un tipo de identificación
			if(!in_array(strtolower($datos['tipo_dni']), $tipos_dni))
				$ind_error['tipo_dni'] = 'Seleccione el Tipo de Identificación.';
		
			## validar identificación
			if(!preg_match($phone_format, $datos['dni']['value']))
				$ind_error['dni'] = 'Ingrese el número de Identificación.';
		
			## el número de identificación es válido, verificar que no exista ya el número en la BD
			else{
				$str_query = 'SELECT * FROM personas WHERE dni != \'' . $datos['dni']['last_value'] .'\' AND dni = \'' . $datos['dni']['value'] . '\'';			
				$verif_dni = $this->Persona->query($str_query);
				## existe una persona con mismo dni
				if(!is_array($verif_dni) || count($verif_dni) != 0)
				$ind_error['dni'] = 'El número de Identificación ya se ha asignado a otra persona.';
				unset($verif_dni, $str_query);
			}			
		} /* end elseif */
		
		## validar teléfono fijo
		if(array_key_exists('telefono_fijo', $datos) && !preg_match($phone_format, $datos['telefono_fijo']))
			$ind_error['telefono_fijo'] = 'Ingrese un número de teléfono válido.';
		
		## validar teléfono movil
		if(array_key_exists('telefono_movil', $datos) && !preg_match($phone_format, $datos['telefono_movil']))
			$ind_error['telefono_movil'] = 'Ingrese un número de teléfono válido.';
		
		## validar email
		if(array_key_exists('email', $datos) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese una dirección de Email válida.';
		
		## validar fecha de nacimiento
		if(array_key_exists('fecha_nac', $datos) && !preg_match($fecha_format, $datos['fecha_nac']))
			$ind_error['fecha_nac'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## verificar que se seleccion el género de la persona
		if(array_key_exists('genero', $datos) && !in_array(strtolower($datos['genero']), $generos))
			$ind_error['genero'] = 'Seleccione el género de la persona.';
		
		## validar dirección de residencia
		if(array_key_exists('direccion_residencia', $datos) && !preg_match($direccion_format, $datos['direccion_residencia']))
			$ind_error['direccion_residencia'] = 'Ingrese sólo letras, números, guiones (- y _), puntos (.), ampersands (&), paréntesis, numerales (#), barras (/) y espacios.';
		
		return $ind_error;
		
	} 
	
	function afterAction () {
		
	}
	
}