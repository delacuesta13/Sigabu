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