<?php

class PerfilesController extends VanillaController {
	
	/**
	 *
	 * @author: Jhon Adrián Cerón <jadrian.ceron@gmail.com>
	 */
	
	function beforeAction () {
				
		session_start();
		
		## El usuario no ha iniciado sesión
		if(!array_key_exists('logueado', $_SESSION) || !$_SESSION['logueado']) {			
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);					
		}
				
	}
	
	function index () {
		
	}
	
	function listar_perfiles ($dni = null) {
		
		## validar que se reciba un dni válido
		if (isset($dni) && preg_match('/^[\d]{5,20}$/', $dni)) {
		
			$data_persona = performAction('personas', 'consultar_persona_fk', array($dni));
			
			## la persona existe
			if (count($data_persona)!=0) {
				
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
						'table' => true, ## es una tabla el nodo padre de este árbol
						'alias' => 'periodo',
						'fields' => array(
							'periodo' => array(
								'text' => 'Periodo',
								'showTable' => true, ## mostrar como columna en la tabla			
								'sort' => true, ## puede ordenarse la tabla por este campo
								'where' => true ## buscar por esta columna
							) /* end periodo */
						) /* end fields */
					), /* end periodos */
					'perfiles' => array(
						'table' => true,
						'alias' => 'perfil',
						'fields' => array(
							'id' => array(
								'showTable' => false,
								'sort' => false,
								'where' => false
							) /* end id */
						) /* end fields */						
					), /* end perfiles */
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
					## realizar los joins entre las tablas
					'join' => array(
						0 => 'periodo.id = perfil.periodo_id',
						1 => 'perfil.perfil_multientidad = multientidad.id'
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
				$str_temp = 'WHERE perfil.persona_dni = \'' . $dni . '\' AND (';
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
				$data_query = $this->Perfil->query($str_query);
				
				## total de tuplas sin LIMIT
				$str_totalquery = 'SELECT FOUND_ROWS() as total';
				$totalreg_query = $this->Perfil->query($str_totalquery); 
				$totalreg_query = $totalreg_query[0]['']['total'];
				
				/**
				 * envío variables a la vista
				 */
				$this->set('dni', $dni);
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
				echo '
				<div class="flash">
					<div class="message notice">
						<p>Vaya! No se ha podido cargar los perfiles de la persona.</p>
					</div>
				</div>
				';
			}
		
		} else {
			echo '
			<div class="flash">
				<div class="message notice">
					<p>Vaya! No se ha podido cargar los perfiles de la persona.</p>
				</div>
			</div>
			';
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	function eliminar ($id = null) {
		
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado'] && $_SESSION['nivel'] >= $GLOBALS['menu_project']['personas']['actions']['perfiles']['nivel']) {
			
			## se recibe un id para eliminar
			if (isset($id) && preg_match('/^[\d]{1,}$/', $id)) {
				$rs = $this->Perfil->eliminar(array($id));
				echo '<div class="message notice"><p>
				Se ha ejecutado exitósamente ' . $rs['trueQuery'] . ' petición (es), de ' .  $rs['totalQuery'] . ' solicitada (s).
				</p></div>';
			}
			## se recibe (n) mediante post, id (s) para eliminar
			elseif (isset($_POST['id']) && is_array($_POST['id']) && count($_POST['id'])!=0) {
				$rs = $this->Perfil->eliminar($_POST['id']);
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
	
	/**
	 *
	 * función llamada vía ajax.
	 * mostrar campos del formulario,
	 * según el perfil de la persona ...
	 */
	function dynamicform () {
	
		## se debe recibir el tipo de perfil a mostrar
		if (isset($_POST['perfil']) && preg_match('/^[\d]{1,}$/', $_POST['perfil'])) {
			
			## consulto el tipo de perfil recibido
			$data_perfil = $this->Perfil->get_multientidad('comunidad_universitaria', $_POST['perfil']);
		
			if (count($data_perfil)!=0) {
				$tipo_perfil = $data_perfil[0]['Multientidad']['nombre'];
				$this->set('tipo_perfil', $tipo_perfil);
				$this->set('lista_jornadas', $this->Perfil->get_multientidad('jornadas'));
				$this->set('lista_programas', $this->lista_programas());
				$this->set('lista_contratos', $this->Perfil->get_multientidad('contratos'));
				$this->set('lista_afinidad', $this->Perfil->get_multientidad('parentescos'));
			}
	
		}
	
		/****************************************************/
	
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
	
		header("Content-Type: text/html; charset=iso-8859-1");
			
	}
	
	function nuevo ($dni = null) {
		
		/**
		 * código que ejecuta javascript del documento que contiene esta vista,
		 * que ha sido cargada vía ajax.
		 * Para saber qué parámetro debe de pasarse, revisar la función js customDialog()
		 * de la vista ver, controlador personas.
		 */
		$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(1);</script>';
		
		## revisar si el usuario tiene permiso para interacutar
		if (array_key_exists('logueado', $_SESSION) && $_SESSION['logueado'] && $_SESSION['nivel'] < $GLOBALS['menu_project']['personas']['actions']['perfiles']['nivel']) {
			$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(2);</script>';
			echo $tag_js;
		}		
		
		## validar que se reciba un dni válido
		if (isset($dni) && preg_match('/^[\d]{5,20}$/', $dni)) {
			
			$data_persona = performAction('personas', 'consultar_persona_fk', array($dni));
			
			##########################################################################
			## la persona existe y está activa #######################################
			if (count($data_persona)!=0 && $data_persona[0]['Persona']['estado']==1) {
				
				$this->set('dni', $dni);
				$this->set('data_persona', $data_persona);
				
				$lista_periodos = performAction('periodos', 'listar_periodos_group_fk', array());
				$this->set('lista_periodos', $lista_periodos);
				
				$lista_perfiles = $this->Perfil->get_multientidad('comunidad_universitaria');
				$this->set('lista_perfiles', $lista_perfiles);
				
			} else {
				## la persona no existe
				if (count($data_persona)==0) {
					$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(0);</script>';
					echo $tag_js;
				} else {
					echo $tag_js;
				}				
			}
			
		} else {
			$tag_js = '<script type="text/JavaScript">window.parent.closeDialog(0);</script>';
			echo $tag_js;
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	/**
	 * 
	 * esta función recibe los
	 * datos del nuevo perfil a crear,
	 * y se encarga de ello ...
	 */
	function crear_perfiles () {
		
		if (isset($_POST['dni'])) {

			$validar_data = array(
				'persona' => $_POST['dni'],
				'periodo' => array(
					'value' => $_POST['fields'][0]['periodo'],
					'new' => true,
					'edit' => false
					),
				'perfil' => $_POST['fields'][1]['perfil']
			);
			
			unset ($_POST['dni'], $_POST['fields'][0], $_POST['fields'][1]);
			
			$_POST['fields'] = array_values($_POST['fields']);
			
			## recorro los campos restantes y los asigno a validar_data
			for ($i = 0; $i < count($_POST['fields']); $i++) {
				foreach ($_POST['fields'][$i] as $field => $value) {
					$validar_data[$field] = $value;
				} /* foreach */				
				unset ($field, $value);
			} /* for */
			
			$fields_to_bd = array(
				"persona" => "persona_dni",
				"periodo" => "periodo_id",
				"perfil" => "perfil_multientidad",
				"jornada" => "jornada_multientidad",
				"programa" => "programa_id",
				"contrato" => "contrato_multientidad",
				"parentesco" => "parentesco_multientidad",
				"apoderado" => "apoderado_dni",
				"semestre" => "semestre"
			);
			$this->set('fields_to_bd', $fields_to_bd);
			
			## envío los datos a revisión, y recibo los (posibles) errores
			$ind_error = $this->validar_data_perfil($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);
			
			## no se encontraron errores
			else{
				
				## tipo de perfil en texto
				$tipo_perfil = $this->Perfil->get_multientidad('comunidad_universitaria', $validar_data['perfil']);
				$tipo_perfil = strtolower($tipo_perfil[0]['Multientidad']['nombre']);
				
				$validar_data['periodo'] = $validar_data['periodo']['value'];
				
				## cambio los nombres de los campos que recibo, a como deberán estar para interactuar con la bd
				foreach ($fields_to_bd as $field_form => $field_db) {
					if (array_key_exists($field_form, $validar_data) && $field_form!=$field_db) {
						$validar_data[$field_db] = $validar_data[$field_form];
						unset($validar_data[$field_form]);
					} elseif(!array_key_exists($field_form, $validar_data)) {
						$validar_data[$field_db] = '';
					}
				} /* foreach */
				unset($field_form, $field_db);
				
				/*
				 * el perfil a asignar es de funcionario, 
				 * y no se selecciona programa académico, 
				 * porque el funcionario es administrativo... 
				 */
				if ($tipo_perfil=='funcionario' && !preg_match('/^[\d]{1,}$/', $validar_data['programa_id'])) {
					$validar_data['programa_id'] = '';
				}
				
				if ($this->Perfil->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
			}
			
		}
		
		/****************************************************/
		
		## función de respuesta ajax
		$this->doNotRenderHeader = 1;
		
		header("Content-Type: text/html; charset=iso-8859-1");
		
	}
	
	private function validar_data_perfil ($datos) {
		
		$ind_error = array();
		
		## formato se valida si se seleccionó una opción
		$select_format = '/^[\d]{1,}$/';
		$semestre_format = '/^[\d]{1,2}$/';
		$dni_format = '/^[\d]{5,20}$/';
		
		## se está creando un nuevo periodo
		if (array_key_exists('new', $datos['periodo']) && $datos['periodo']['new']) {
			## validar que se haya seleccionado un periodo
			if (preg_match($select_format, $datos['periodo']['value'])) {
				## revisar que no existe ya, un perfil en el periodo a crear
				$tmp_query = $this->Perfil->consultar_perfil_periodo($datos['persona'], $datos['periodo']['value']);
				if(count($tmp_query)!=0)
					$ind_error['periodo'] = 'Se ha asignado el perfil de <i>' . $tmp_query[0]['Multientidad']['nombre'] . '</i>'.
					' en el periodo (' . $tmp_query[0]['Periodo']['periodo'] . ') a asignar.';
				unset($tmp_query);
			} else {
				$ind_error['periodo'] = 'Seleccione un Periodo.';
			}
		}
		
		## 1-> seleccionó un perfil
		$seleccion_perfil = 0;
		
		## validar selección de un perfil
		if (!preg_match($select_format, $datos['perfil'])) { 
			$ind_error['perfil'] = 'Seleccione un Perfil.';
		} else{
			$seleccion_perfil = 1;
			## tipo de perfil en texto
			$tipo_perfil = $this->Perfil->get_multientidad('comunidad_universitaria', $datos['perfil']);
			$tipo_perfil = strtolower($tipo_perfil[0]['Multientidad']['nombre']);
		}
		
		## validar selección de jornada
		if (array_key_exists('jornada', $datos) && !preg_match($select_format, $datos['jornada']))
			$ind_error['jornada'] = 'Seleccione una Jornada.';
		
		## validar selección de un programa
		/**
		 * sólo los funcionarios pueden no tener asignado un programa,
		 * en caso de que pertenezcan al área administrativa.
		 */
		if ($seleccion_perfil == 1 && $tipo_perfil!='funcionario' && array_key_exists('programa', $datos) && !preg_match($select_format, $datos['programa']))
			$ind_error['programa'] = 'Seleccione un Programa Acad&eacute;mico.';
		
		## validar semestre
		if (array_key_exists('semestre', $datos) && !preg_match($semestre_format, $datos['semestre']))
			$ind_error['semestre'] = 'Ingrese un n&uacute;mero v&aacute;lido.';
		
		## validar selección de contrato
		if (array_key_exists('contrato', $datos) && !preg_match($select_format, $datos['contrato']))
			$ind_error['contrato'] = 'Seleccione un tipo de Contrato.';
		
		## validar selección de parentesco
		if (array_key_exists('parentesco', $datos) && !preg_match($select_format, $datos['parentesco']))
			$ind_error['parentesco'] = 'Seleccione un tipo de Parentesco.';
		
		## validar que ingrese dni del apoderado
		if(array_key_exists('apoderado', $datos) && !preg_match($dni_format, $datos['apoderado']))
			$ind_error['apoderado'] = 'Ingrese un n&uacute;mero de Identificaci&oacute;n.';
		
		## ingresó el DNI, validar que el apoderado exista y esté activo
		elseif (array_key_exists('apoderado', $datos) && preg_match($dni_format, $datos['apoderado'])) {
			$data_apoderado = performAction('personas', 'consultar_persona_fk', array($datos['apoderado']));
			## el apoderado no existe
			if (count($data_apoderado)==0) {
				$ind_error['apoderado'] = 'No existe persona con la identificaci&oacute;n ingresada.';
			} elseif ($data_apoderado[0]['Persona']['estado']!=1) { ## la apoderado no está activo
				$ind_error['apoderado'] = 'El apoderado no est&aacute; activo.';
			}
			unset($data_apoderado);
		}
		
		return $ind_error;
		
	} 
	
	##############################################################
	## Programas #################################################
	##############################################################
	
	/**
	 * 
	 * retorna los programas académicos,
	 * agrupados por facultad ...
	 */
	function lista_programas () {
		$ord_programas = array();
		$lista_programas = $this->Perfil->get_programas();
		$str_facultad = '';
		$str_temp = '';
		for ($i = 0; $i < count($lista_programas); $i++) {
			$str_facultad = $lista_programas[$i]['Facultad']['nombre'];
			$ord_programas[$str_facultad] = array();
			$ord_programas[$str_facultad]['nombre'] = $lista_programas[$i]['Facultad']['nombre'];
			$ord_programas[$str_facultad]['abrev'] = $lista_programas[$i]['Facultad']['abrev'];
			$ord_programas[$str_facultad]['programas'] = array();
			for ($j = $i; $j < count($lista_programas); $j++) {
				$str_temp = $lista_programas[$j]['Facultad']['nombre'];
				if ($str_facultad==$str_temp) {
					$ord_programas[$str_facultad]['programas'][] = array(
						'id' => $lista_programas[$j]['Programa']['id'],
						'nombre' => $lista_programas[$j]['Programa']['nombre'],
						'abrev' => $lista_programas[$j]['Programa']['abrev']
					);
				} else {
					break;
				}
			} /* for j*/
			$j--;
			$i = $j;
		} /* for i */
		return $ord_programas;
	}
	
	function afterAction () {
		
	}
	
}