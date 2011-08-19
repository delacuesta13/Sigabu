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