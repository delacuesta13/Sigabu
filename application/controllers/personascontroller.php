<?php

class PersonasController extends VanillaController{
	
	/**
	 * 
	 * @author: Jhon Adri�n Cer�n <jadrian.ceron@gmail.com> 
	 */
	
	function beforeAction () {
		
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
			 * en el men� del proyecto y tiene un nivel
			 * m�nimo de permiso. Si no se ha definido la
			 * 'action' en el men� del proyecto, se infiere
			 * que el nivel de permiso necesario para �sta 
			 * es el nivel m�nimo exigido por el controlador 
			 * donde est� dicha 'action'.
			 */
			
			## El controlador no se ha definido en el men� del proyecto
			if (!array_key_exists(strtolower($this->_controller), $GLOBALS['menu_project'])){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '404'));
			}
			
			/*
			 * No se defini� la 'action' en el men� de 'actions'
			 * del controlador, o no se defini� el nivel de permiso
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
			if($_SESSION['nivel'] < $GLOBALS['menu_project'][strtolower($this->_controller)]['actions'][$this->_action]['nivel']){
				redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action'], array('error', '1'));
			}			
			
		}
		
		## El usuario no ha iniciado sesi�n
		else{
			redirectAction($GLOBALS['default_controller'], $GLOBALS['default_action']);
		}		
		
	}
	
	function index () {
		
	}
	
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
			
			## ingres� el tel�fono fijo
			if(isset($_POST['telefono_fijo']) && strlen($_POST['telefono_fijo'])!=0)
				$validar_data['telefono_fijo'] = $_POST['telefono_fijo'];
			
			## ingres� el tel�fono m�vil
			if(isset($_POST['telefono_movil']) && strlen($_POST['telefono_movil'])!=0)
				$validar_data['telefono_movil'] = $_POST['telefono_movil'];
			
			## ingres� email
			if(isset($_POST['email']) && strlen($_POST['email'])!=0)
				$validar_data['email'] = $_POST['email'];
			
			## ingres� fecha de nacimiento
			if(isset($_POST['fecha_nac']) && strlen($_POST['fecha_nac'])!=0)
				$validar_data['fecha_nac'] = $_POST['fecha_nac'];
			
			## ingres� direcci�n de residencia
			if(isset($_POST['direccion_residencia']) && strlen($_POST['direccion_residencia'])!=0)
				$validar_data['direccion_residencia'] = $_POST['direccion_residencia'];
			
			## activ� a la persona como monitor
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
			
			## env�o los datos a revisi�n, y recibo los (posibles) errores
			$ind_error = $this->validar_data_persona($validar_data);
			if(is_array($ind_error) && count($ind_error)!=0)
				$this->set('ind_error', $ind_error);			
			
			## no se recibieron errores
			else{
				
				## limpio la direcci�n de residencia, si se declar�, evitando sql injection
				if(array_key_exists('direccion_residencia', $validar_data))	
					$validar_data = addslashes($validar_data['direccion_residencia']);
			
				$validar_data['dni'] = $validar_data['dni']['value'];
				
				if ($this->Persona->nuevo($validar_data)) {
					$this->set('rs_crear', true);
				} else {
					$this->set('rs_crear', false);
				}
				
				
			}/* else */
		
		} /* env�o del formulario */
		
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
	
	/**
	 * 
	 * Validar los datos de las personas ...
	 * @param array $datos
	 */
	private function validar_data_persona($datos){
		
		$ind_error = array();
		
		$letras_format = '/^[a-zA-Z ������������]{3,45}$/';
		$fecha_format = '/^[\d]{4}-((0[1-9])|(1[0-2]))-((0[1-9])|([1-2]\d)|(3[0-1]))$/';
		$phone_format = '/^[\d]{5,20}$/';
		$tipos_dni = array("cc", "ce", "ti", "rc");
		$generos = array("h", "m");
		
		## validar nombres
		if(array_key_exists('nombres', $datos) && !preg_match($letras_format, $datos['nombres']))
			$ind_error['nombres'] = 'Ingrese s�lo letras y espacios.';
		
		## validar apellidos
		if(array_key_exists('apellidos', $datos) && !preg_match($letras_format, $datos['apellidos']))
			$ind_error['apellidos'] = 'Ingrese s�lo letras y espacios.';
		
		/*
		 * dni es un array, y si el key (dentro de �ste) 'new' es true
		 * se valida tipo_dni y dni, porque se va a crear una nueva persona.
		 */
		if(array_key_exists('dni', $datos) && is_array($datos['dni']) && $datos['dni']['new']){			
			## validar que se haya seleccionado un tipo de identificaci�n
			if(!in_array(strtolower($datos['tipo_dni']), $tipos_dni))
				$ind_error['tipo_dni'] = 'Seleccione el Tipo de Identificaci�n.';
		
			## validar identificaci�n
			if(!preg_match($phone_format, $datos['dni']['value']))
				$ind_error['dni'] = 'Ingrese el n�mero de Identificaci�n.';
			
			## el n�mero de identificaci�n es v�lido, verificar que no exista ya el n�mero en la BD
			else{
				$verif_dni = $this->consultar_persona_fk($datos['dni']['value']);
				## existe una persona con mismo dni
				if(!is_array($verif_dni) || count($verif_dni) != 0)
					$ind_error['dni'] = 'El n�mero de Identificaci�n ya se ha asignado a otra persona.';
				unset($verif_dni);
			}			
		} /* end dni */
		
		## validar tel�fono fijo
		if(array_key_exists('telefono_fijo', $datos) && !preg_match($phone_format, $datos['telefono_fijo']))
			$ind_error['telefono_fijo'] = 'Ingrese un n�mero de tel�fono v�lido.';
		
		## validar tel�fono movil
		if(array_key_exists('telefono_movil', $datos) && !preg_match($phone_format, $datos['telefono_movil']))
			$ind_error['telefono_movil'] = 'Ingrese un n�mero de tel�fono v�lido.';
		
		## validar email
		if(array_key_exists('email', $datos) && !filter_var($datos['email'], FILTER_VALIDATE_EMAIL))
			$ind_error['email'] = 'Ingrese una direcci�n de Email v�lida.';
		
		## validar fecha de nacimiento
		if(array_key_exists('fecha_nac', $datos) && !preg_match($fecha_format, $datos['fecha_nac']))
			$ind_error['fecha_nac'] = '(AAAA-MM-DD) El formato de la fecha es incorrecto.';
		
		## verificar que se seleccion el g�nero de la persona
		if(array_key_exists('genero', $datos) && !in_array(strtolower($datos['genero']), $generos))
			$ind_error['genero'] = 'Seleccione el g�nero de la persona.';
		
		## validar direcci�n de residencia
		if(array_key_exists('direccion_residencia', $datos) && !preg_match('/^[\w]{5,60}$/', $datos['direccion_residencia']))
			$ind_error['direccion_residencia'] = 'Ingrese una direcci�n v�lida.';
		
		return $ind_error;
		
	} 
	
	function afterAction () {
		
	}
	
}