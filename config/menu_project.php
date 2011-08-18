<?php

/**
 * 
 *  NOTA: Definir todas las 'actions' de los
 *  controladores que interact�an con el usuario, 
 *  es decir, que tienen su interfaz en el sistema,
 *  mu�strense o no en el men� secundario. Si no se
 *  definen, para efectos de permiso se toma el default
 *  del controlador donde �sta la 'action'.
 */

$menu_project = array(

	"personas" => array(		
		"ico" => "controllers/personas.png",
		"desc" => "Gestiona las personas de la Comunidad Universitaria y su perfiles dentro de �sta",
		"nivel" => 2, ## nivel m�nimo exigido por una acci�n del controlador				
		"actions" => array(
					
			"index" => array(
				"text" => "Personas",
				"nivel" => 2,
				"showMenu" => true 
			), /* end index */
			"nuevo" => array(
				"text" => "Nuevo",
				"nivel" => 3,
				"showMenu" => true	
			), /* end nuevo*/
			"editar" => array(
				"nivel" => 3,
				"showMenu" => false
			), /* end editar */
			"eliminar" => array(
				"nivel" => 5,
				"showMenu" => false
			) /* end eliminar */			
						
		) /* end actions */						
	), /* end personas */

	"actividades" => array(
		"ico" => "controllers/actividades.png",
		"desc" => "Gestiona las actividades de Bienestar U",
		"nivel" => 2, ## nivel m�nimo exigido por una acci�n del controlador
		"actions" => array(
		
			"index" => array(
				"text" => "Actividades",
				"nivel" => 2,
				"showMenu" => true
			), /* end index */
			"eliminar" => array(
				"nivel" => 5,
				"showMenu" => false
			), /* end eliminar */
			"nuevo" => array(
				"nivel" => 5,
				"showMenu" => false
			), /* end nuevo */
			"editar" => array(
				"nivel" => 5,
				"showMenu" => false
			) /* end editar */
		
		) /* end actions */	
	), /* end actividades */
	
	"lugares" => array(
		"ico" => "controllers/lugares.png",
		"desc" => "Gestiona los lugares o espacios deportivos/culturales utilizados por Bienestar U",
		"nivel" => 3, ## nivel m�nimo exigido por una acci�n del controlador
		"actions" => array(
		
		
		
		) /* end actions */
	), /* end lugares */
	
	"usuarios" => array(
		"ico" => "controllers/usuarios.png",
		"desc" => "Gestiona las cuentas de usuarios",
		"nivel" => 5, ## nivel m�nimo exigido por una acci�n del controlador
		"actions" => array(
		
		
		
		) /* end actions */
	) /* end usuarios */

);

/**
* Declaro las variable como global,
* de tal forma que, cuando se vaya
* a usar se debe declarar as�:
* $GLOBALS['menu_project']
*/
$GLOBALS['menu_project'] = $menu_project;