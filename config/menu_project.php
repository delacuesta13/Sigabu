<?php

$menu_project = array(

	"personas" => array(		
		"ico" => "",
		"desc" => "",
		"nivel" => 3, ## nivel mínimo exigido por una acción del controlador				
		"actions" => array(
					
			"index" => array(
				"text" => "Personas",
				"nivel" => 3,
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
		"ico" => "",
		"desc" => "",
		"nivel" => 2, ## nivel mínimo exigido por una acción del controlador
		"actions" => array(
		
		
		
		) /* end actions */	
	), /* end actividades */
	
	"lugares" => array(
		"ico" => "",
		"desc" => "",
		"nivel" => 3, ## nivel mínimo exigido por una acción del controlador
		"actions" => array(
		
		
		
		) /* end actions */
	), /* end lugares */
	
	"usuarios" => array(
		"ico" => "",
		"desc" => "",
		"nivel" => 5, ## nivel mínimo exigido por una acción del controlador
		"actions" => array(
		
		
		
		) /* end actions */
	) /* end usuarios */

);

/**
* Declaro las variable como global,
* de tal forma que, cuando se vaya
* a usar se debe declarar así:
* $GLOBALS['menu_project']
*/
$GLOBALS['menu_project'] = $menu_project;