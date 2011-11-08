<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class UsuariosController extends VanillaController {
	
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
		
		$tag_css = '
		table tr td a {
			text-decoration: underline;
		}
		';
		$this->set('make_tag_css', $tag_css);
		
		$tag_js = '
		var info_preload = \'<div id="info_preload" class="dataTablas_preload">Cargando...</div>\';
		
		function load_dataTable (pag, sort, order) {
			$(function() {
				$( "#dynamic" ).html( info_preload );
				var url = "'. BASE_PATH . '/'. strtolower($this->_controller) . '/' . 'listar_usuarios' .'";
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
		
			load_dataTable(1, \'\', \'\');
			
			$( "#reg_pag" ).change(function() {
				load_dataTable(1, \'\', \'\');
			});
			
			$( "#search" ).bind("keyup", function() {
				load_dataTable(1, \'\', \'\');
			});
			
		});
		';		
		$this->set('make_tag_js', $tag_js);
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min'));
		
	}
	
	function listar_usuarios () {
		
		$parametros = func_get_args();
		
		/**
		 * 
		 * empezar a ordenar por este campo ...
		 * 	<alias>tabla.campo
		 * @var string
		 */
		$campo_dft = 'usuario.username';
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
			'personas' => array(
		 		'table' => true,
		 		'alias' => 'persona',
		 		'fields' => array(
		 			'dni' => array(
		 				'text' => 'Identificación',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => true
		 			), /* end dni */
		 			'nombres' => array(
			 			'text' => 'Persona',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => true
		 			), /* end nombres */
		 			'apellidos' => array(
		 				'showTable' => false,
		 				'sort' => false,
		 				'where' => true
		 			) /* end apellidos */
		 		) /* end fields */
		 	), /* end personas */
		 	'roles' => array(
		 		'table' => true,
		 		'alias' => 'rol',
		 		'fields' => array(
		 			'nombre' => array(
		 				'text' => 'Rol',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => true
		 			) /* end nombre */
		 		) /* end fields */
		 	), /* end roles */
		 	'usuarios' => array(
		 		'table' => true,
		 		'alias' => 'usuario',
		 		'fields' => array(
		 			'username' => array(
		 				'text' => 'Usuario',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => true
		 			), /* end username */
		 			'email' => array(
		 				'text' => 'Email',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => true
		 			), /* end email */
		 			'estado' => array(	
		 				'text' => 'Estado',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => false
		 			), /* estado */
		 			'fecha_activacion' => array(
		 				'text' => 'Fecha Activación',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => false
		 			), /* end fecha_activacion */
		 			'ultima_visita' => array(
		 				'text' => 'Última Visita',
		 				'showTable' => true,
		 				'sort' => true,
		 				'where' => false
		 			) /* end ultima_visita */
		 		) /* end fields */
		 	), /* end usuario */
		 	'join' => array(
		 		0 => 'usuario.rol_id = rol.id',
		 		1 => 'usuario.persona_dni = persona.dni'
		 	) /* end join */
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
		$data_query = $this->Usuario->query($str_query);
		
		## total de tuplas sin LIMIT
		$str_totalquery = 'SELECT FOUND_ROWS() as total';
		$totalreg_query = $this->Usuario->query($str_totalquery); 
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
	
	function eliminar () {
		
		## el usuario tiene permiso para eliminar
		if ($_SESSION['nivel'] >= $GLOBALS['menu_project'][strtolower($this->_controller)]['nivel']) {
			## se recibe (n) mediante post, persona (s) para eliminar
			if (isset($_POST['persona']) && is_array($_POST['persona']) && count($_POST['persona'])!=0) {
				## revisar que no se vaya a eliminar el usuario activo
				$delete_self = false;
				for ($i = 0; $i < count($_POST['persona']); $i++) {
					if ($_POST['persona'][$i]==$_SESSION['persona_dni']) {
						$delete_self = true;
						break;
					}					
				}
				if (!$delete_self) {		
					$rs = $this->Usuario->eliminar($_POST['persona']);
					echo '<div class="message notice"><p>
					Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
					</p></div>';
				} else {
					echo '<div class="message notice"><p>No se puede eliminar usted mismo.</p></div>';
				}
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
	
	function nuevo () {
		
		## se ha envíado el formulario
		if (isset($_POST['persona'], $_POST['usuario'], $_POST['password'], $_POST['confirm_password'], $_POST['email'], $_POST['rol'])) {
			
			$validar_data = array(
				'query' => 'new',
				'persona' => $_POST['persona'],
				'usuario' => $_POST['usuario'],
				'password' => $_POST['password'],
				'confirm_password' => $_POST['confirm_password'],
				'email' => $_POST['email'],
				'rol' => $_POST['rol']
			);
			
			## el usuario estará activo
			if (isset($_POST['estado'])) {
				$validar_data['estado'] = 1;
			} else {
				$validar_data['estado'] = 0;
				## se ingresó una fecha de activación
				if (strlen($_POST['fecha_activacion'])!=0)
					$validar_data['fecha_activacion'] = $_POST['fecha_activacion'];
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_usuario($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);

			## no se recibieron errores
			else {
				
				$validar_data['persona_dni'] = $validar_data['persona'];
				$validar_data['username'] = $validar_data['usuario'];
				$validar_data['rol_id'] = $validar_data['rol'];
				$validar_data['password'] = md5($validar_data['password']);
				
				unset($validar_data['query'], $validar_data['persona'], $validar_data['usuario'], $validar_data['confirm_password'], $validar_data['rol']);
				
				if ($this->Usuario->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			} /* else */
		
		} /* envío del formulario */
		
		$tag_js = '
		
		$(document).ready(function() {
			
			$("a.cancel").click(function(){
				document.forms["formulario"].reset();
			}); 
			
			$( "#fecha_activacion" ).datepicker({
				regional: "es",
				dateFormat: "yy-mm-dd",				
				changeMonth: true,
				changeYear: true,
				showOtherMonths: true,
				selectOtherMonths: false
			});
			
			$("#estado").click(function() {
				$("#div_fecha").toggle();
			});

			$("#usuario[title], #password[title]")
			.qtip({
				content: {
					title: {
						text: "Información",
                  		button: true
					}
				},
				position: {
					my: "left center", 
					at: "right center"
				},
				style: {
					classes: "ui-tooltip-dark"
				},
				show: {
					event: "focus"
				},
				hide: {
      				event: false
   				}
			});
			
			showTip(\'usuario\', 0);
			showTip(\'password\', 1);
			
			$("#usuario").keyup(function (){
				showTip(\'usuario\', 0);			
			});
			
			$("#password").keyup(function (){
				showTip(\'password\', 1);
			});
			
		});
		';
		
		$this->set('make_tag_js', $tag_js);
		
		$this->set('lista_roles', $this->listar_roles());
		
		$this->set('makecss', array('jquery.qtip.min'));
		$this->set('makejs', array('jquery.qtip.min', 'jquery.ui.datepicker-es', 'usuarios'));
		
	}
	
	function editar ($persona = null, $usuario = null) {

		$editar = false;
		
		## se envió el formulario
		if (isset($_POST['email'], $_POST['rol'])) {
			
			$validar_data = array(
				'query' => 'edit',
				'email' => $_POST['email'],
				'rol' => $_POST['rol']
			);
			
			## si no se deja el campo password vacío, se va a editar
			if (strlen($_POST['password'])!=0) {
				$validar_data['password'] = $_POST['password'];	
				$validar_data['confirm_password'] = $_POST['confirm_password'];	
			}
			
			## el usuario estará activo
			if (isset($_POST['estado'])) {
				$validar_data['estado'] = 1;
			} else {
				$validar_data['estado'] = 0;
				if (strlen($_POST['fecha_activacion'])!=0)
					$validar_data['fecha_activacion'] = $_POST['fecha_activacion'];
			}
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_usuario($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se recibieron errores
			else {
				
				## validar que el usuario no se vaya a editar a sí mismo
				if ($_SESSION['persona_dni']==$persona) {
					$this->set('edit_self', true);
				} else {
					
					$validar_data['rol_id'] = $validar_data['rol'];
					
					unset($validar_data['query'], $validar_data['rol']);
					
					## el usuario estará activo, entonces edito fecha_activacion como NULL
					if ($validar_data['estado']==1)
						$validar_data['fecha_activacion'] = '';
					
					## si se edita la password, elimino el campo de confirmación de la data a enviar
					if (array_key_exists('password', $validar_data)) {
						$validar_data['password'] = md5($validar_data['password']);
						unset ($validar_data['confirm_password']);
					}
					
					if ($this->Usuario->editar($persona, $validar_data)) {
						$editar = true;
					} else {
						$this->set('rs_editar', false);
					}
					
				} /* else */
				
			} /* else */
			
		} /* envío del formulario */
		
		## se recibe el dni de la cuenta de usuario
		if (isset($persona) && preg_match('/^[\d]{5,20}$/', $persona) && !$editar) {
			
			## consulto la cuenta de usuario
			$data_usuario = $this->Usuario->consultar_usuario($persona);
		
			##la persona tiene una cuenta de usuario
			if (count($data_usuario)!=0) {
				
				## no se recibe el nombre de usuario o éste no es válido
				if (!isset($usuario) || $usuario!=strtolower($data_usuario[0]['Usuario']['username'])) {
					redirectAction(strtolower($this->_controller), $this->_action, array($persona, strtolower($data_usuario[0]['Usuario']['username'])));
				}
				#############################################################################################################################
				## Código propio de la 'action' #############################################################################################
				#############################################################################################################################
				
				$this->set('persona', $persona);
				$this->set('usuario', $usuario);
				$this->set('data_usuario', $data_usuario);
				
				$tag_js = '
		
				$(document).ready(function() {
			
				$("a.cancel").click(function(){
					document.forms["formulario"].reset();
				}); 
			
				$( "#fecha_activacion" ).datepicker({
					regional: "es",
					dateFormat: "yy-mm-dd",				
					changeMonth: true,
					changeYear: true,
					showOtherMonths: true,
					selectOtherMonths: false
				});
			
				$("#estado").click(function() {
					$("#div_fecha").toggle();
				});

				$("#password[title]")
				.qtip({
					content: {
						title: {
							text: "Información",
                  			button: true
						}
					},
					position: {
						my: "left center", 
						at: "right center"
					},
					style: {
						classes: "ui-tooltip-dark"
					},
					show: {
						event: "focus"
					},
					hide: {
      					event: false
   					}
				});
			
				showTip(\'password\', 0);
			
				$("#password").keyup(function (){
					showTip(\'password\', 0);
				});
			
			});
			';
		
			$this->set('make_tag_js', $tag_js);
		
			$this->set('lista_roles', $this->listar_roles());
		
			$this->set('makecss', array('jquery.qtip.min'));
			$this->set('makejs', array('jquery.qtip.min', 'jquery.ui.datepicker-es', 'usuarios'));
				
				#############################################################################################################################
			} else {
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			} /* else */
			
		} /* if */
		
		## se editó exitósamente
		elseif ($editar) {
			redirectAction(strtolower($this->_controller), 'index');
		}
		
		## no se recibieron datos 
		else {
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
		}
		
	} 
	
	private function validar_data_usuario ($datos) {
		
		$ind_error = array();
		
		$dni_format = '/^[\d]{5,20}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$select_format = '/^[\d]{1,}$/';
		$usuario_format = $this->patron_campos('usuario');
		$password_format = $this->patron_campos('password');
		
		## se va a crear una nueva cuenta de usuario
		if (strtolower($datos['query'])=='new') {
			
			## validar el número de identificación
			if (!preg_match($dni_format, $datos['persona']))
				$ind_error['persona'] = 'Ingrese un número de identificación válido.';
		
			## el número de identificación es válido
			else {
				## recojo los datos de la persona
				$data_persona = performAction('personas', 'consultar_persona_fk', array($datos['persona']));
				## la persona existe
				if (count($data_persona)!=0) {
					## la persona está activa
					if ($data_persona[0]['Persona']['estado']==1) {
						## revisar si ya se le asignó una cuenta de usuario
						if (count($this->Usuario->query('SELECT * FROM usuarios WHERE persona_dni = \'' . mysql_real_escape_string($datos['persona']) . '\''))!=0)
							$ind_error['persona'] = 'Ya se ha asignado una cuenta de usuario a la persona.';
					} else {
						$ind_error['persona'] = 'El número de identificación ingresado corresponde a una persona que no está activa.';
					} /* else */
				} else {
					$ind_error['persona'] = 'El número de identificación ingresado no corresponde a ninguna persona.';
				} /* else */
			} /* else */
			
			## validar el nombre de usuario
			if (!preg_match($usuario_format['regex'], $datos['usuario']))
				$ind_error['usuario'] = $usuario_format['error'];
			
			## el nombre de usuario ingresado es válido
			else {
				## verificar que no exista ya un nombre de usuario igual en el sistema
				if (count($this->Usuario->query('SELECT * FROM usuarios WHERE username = \'' . mysql_real_escape_string($datos['usuario']) . '\''))) {
					$ind_error['usuario'] = 'El nombre de usuario ya se ha asignado.';
				} /* if */
			} /* else */
			
			## validar la password
			if (!preg_match($password_format['regex'], $datos['password']))
				$ind_error['password'] = $password_format['error'];
			
			## se ingresó un password válido
			else {
				## los passwords no coinciden
				if ($datos['password']!=$datos['confirm_password'])
					$ind_error['confirm_password'] = 'Las passwords no coinciden.';
			} /* else */			
			
		} /* new */
		
		## se va a editar un usuario
		elseif ($datos['query']=='edit') {
			## se va a editar la password
			if (array_key_exists('password', $datos)) {
				## validar la password
				if (!preg_match($password_format['regex'], $datos['password'])) {
					$ind_error['password'] = $password_format['error'];
				} else {
					## las passwords no coinciden
					if ($datos['password']!=$datos['confirm_password'])
						$ind_error['password'] = 'Las passwords no coinciden';
				} /* else */
			} /* if */
		} /* elseif */
		
		## validar email
		if (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese un email válido';
		
		## validar selección de un rol de usuario
		if (!preg_match($select_format, $datos['rol']))
			$ind_error['rol'] = 'Seleccione un rol de usuario';
		
		## el usuario estará inactivo
		if ($datos['estado']==0 && array_key_exists('fecha_activacion', $datos) && !preg_match($fecha_format, $datos['fecha_activacion']))
			$ind_error['fecha_activacion'] = '(AAAA-MM-DD) El formato de fecha es incorrecto.';
		
		return $ind_error;
		
	}
	
	/**
	 * 
	 * Esta no es una función que retorne
	 * datos JSON. Sólo retorna la regex
	 * (expresión regular) de un campo de
	 * usuarios.
	 */
	function patron_campos ($campo = null) {
		$regex = array(
			'usuario' => array(
				'regex' => '/^[a-zA-Z0-9_-]{4,16}$/',
				'error' => 'S&oacute;lo caracteres alfan&uacute;mericos y guiones.', ## mensaje si no coincide con el patrón
				'min' => 4, ## mínimo 4 caracteres
				'max' => 16
			),
			'password' => array(
				'regex' => '/^[a-zA-Z0-9\.\!\$\=\+,@#&_-]{8,20}$/',
				'error' => 'S&oacute;lo caracteres alfan&uacute;mericos, guiones y caracteres especiales (.,!@#$&=+).',
				'min' => 8,
				'max' => 20
			)		
		);
		if (isset($campo) && preg_match('/^[a-z]{2,}$/', strtolower($campo)) && array_key_exists(strtolower($campo), $regex)) {
			return $regex[$campo];
		} else {
			return false;
		}
	}
	
	###########################################
	## Roles ##################################
	###########################################
	
	function listar_roles () {
		return $this->Usuario->listar_roles();
	}
	
	/**
	 * 
	 * Obtener el rol de un usuario ...
	 * @param int $dniUsuario
	 */
	function getRol ($dniUsuario = null) {
		$rol = array();
		if (isset ($rol) && preg_match('/^[\d]{5,20}$/', $dniUsuario)) {
			$temp = $this->Usuario->getRol($dniUsuario);
			$rol['usuario'] = $temp[0]['Usuario']['username'];
			$rol['rol'] = $temp[0]['Rol']['nombre'];
			$rol['nivel'] = $temp[0]['Rol']['permiso'];
		}
		return $rol;
	}
	
	###########################################
	## Funciones JSON #########################
	###########################################
	
	/**
	 * Función que valida los datos
	 * de una cuenta de usuario, sea
	 * antes de crear o editar.
	 */
	function valida_datos () {
		
		$datos = array();
		
		if (isset($_POST['campo'], $_POST['valor']) && preg_match('/^[a-z]{2,}$/', strtolower($_POST['campo']))) {
			$campo = strtolower($_POST['campo']);
			$valor = $_POST['valor'];
			
			## validar el campo ususario
			if ($campo == 'usuario') {
				## recibo los datos relativos al campo
				$usuario_format = $this->patron_campos($campo);
				## la longitud del valor recibido está entre el mínimo y el máximo del campo
				if ($usuario_format['min'] <= strlen($valor) && strlen($valor) <= $usuario_format['max']) {
					## el valor del campo es válido
					if (preg_match($usuario_format['regex'], $valor)) {
						$sql = '';
						## el nombre de usuario está en uso
						if (count($this->Usuario->query('SELECT * FROM usuarios WHERE username = \'' . mysql_real_escape_string($valor) . '\''))!=0) {
							$datos['response'] = array(
								'type' => 'error',
								'message' => 'El nombre de usuario ya est&aacute; en uso.'
							);
						} else {
							$datos['response'] = array(
								'type' => 'success',
								'message' => 'El nombre de usuario es v&aacute;lido y no est&aacute; en uso.'
							);
						}
					} else {
						$datos['response'] = array(
							'type' => 'error',
							'message' => $usuario_format['error']
						);
					}					
				} elseif (strlen($valor)==0) {
					$datos['response'] = array(
						'type' => 'default',
						'message' => 'Ingrese un nombre usuario.'
					);
				} else {
					$datos['response'] = array(
						'type' => 'error',
						'message' => 'Este campo debe tener entre ' . $usuario_format['min'] . ' y ' . $usuario_format['max'] .' caracteres.'
					);
				}
			} elseif ($campo == 'password') {
				## recibo los datos relativos al campo
				$password_format = $this->patron_campos($campo);
				## la longitud del password está dentro del rango
				if ($password_format['min'] <= strlen($valor) && strlen($valor) <= $password_format['max']) {
					## el valor del campo coincide con el patrón
					if (preg_match($password_format['regex'], $valor)) {
						$datos['response'] = array(
							'type' => 'success',
							'message' => 'La password es v&aacute;lida.'
						);
					} else {
						$datos['response'] = array(	
							'type' => 'error',
							'message' => $password_format['error']
						);
					}
				} elseif (strlen($valor)==0) {
					$datos['response'] = array(
						'type' => 'default',
						'message' => 'Ingrese una password.'
					);
				} else {
					$datos['response'] = array(	
						'type' => 'error',
						'message' => 'Este campo debe tener entre ' . $password_format['min'] . ' y ' . $password_format['max'] .' caracteres.'
					);
				}		
			} else {
				## no se recibe un campo válido
				$datos['response'] = array(
					'type' => 'error',
					'message' => 'No se recibieron datos.'
				); 
			} /* else */
		} else {
			## no se recibieron los datos necesario para validar
			$datos['response'] = array(
				'type' => 'error',
				'message' => 'No se recibieron datos. '
			); 
		}
		
		/**
 		 * Send as JSON
 		 */
		header("Content-Type: application/json", true);
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		/* Retornar JSON */
		echo json_encode($datos);
		
	}
	
	function afterAction () {
		
	}
	
}