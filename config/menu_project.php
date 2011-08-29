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
 *  NOTA: Definir todas las 'actions' de los
 *  controladores que interactúan con el usuario, 
 *  es decir, que tienen su interfaz en el sistema,
 *  muéstrense o no en el menú secundario. Si no se
 *  definen, para efectos de permiso se toma el default
 *  del controlador donde ésta la 'action'.
 */

$menu_project = array(

	"personas" => array(
		"show" => true, ## mostrar controlador en menú y dashboard
		"text" => "Personas",		
		"ico" => "controllers/personas.png",
		"desc" => "Gestiona las personas de la Comunidad Universitaria y su perfiles dentro de ésta",
		"nivel" => 2, ## nivel mínimo exigido por una acción del controlador				
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
			), /* end eliminar */
			## con esta definición, se trabajará con todo el controlador perfiles
			"perfiles" => array(	
				"nivel" => 3,
				"showMenu" => false
			) /* end perfiles */			
						
		) /* end actions */						
	), /* end personas */

	"actividades" => array(
		"show" => true,
		"text" => "Actividades",
		"ico" => "controllers/actividades.png",
		"desc" => "Gestiona las actividades de Bienestar U",
		"nivel" => 2, ## nivel mínimo exigido por una acción del controlador
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
			), /* end editar */
			
			## accesos directos a otros controladores
			"programacion" => array(
				"text" => "Programación",
				"nivel" => 2,
				"showMenu" => true
			),
			"periodos" => array(
				"text" => "Períodos",
				"nivel" => 3,
				"showMenu" => true
			) /* end periodos */
			
		) /* end actions */	
	), /* end actividades */
	
	"programacion" => array(
		"show" => false,
		"nivel" => 2,
		"actions" => array(
		
			"index" => array(
				"text" => "Programación",
				"nivel" => 2,
				"showMenu" => true
			), /* end index */
			"nuevo" => array(
				"text" => "Nuevo",
				"nivel" => 4,
				"showMenu" => true
			), /* end nuevo */
			"editar" => array(
				"nivel" => 4,
				"showMenu" => false
			), /* end editar */
			"eliminar" => array(
				"nivel" => 4,
				"showMenu" => false
			) /* end eliminar */
		
		) /* end actions */
	), /* end programacion */
	
	"periodos" => array(
		"show" => false,
		"text" => "Períodos",
		"ico" => "controllers/periodos.png",
		"desc" => "Gestiona los períodos académicos de la Universidad",
		"nivel" => 3, ## nivel mínimo exigido por una acción del controlador
		"actions" => array(
	
			"index" => array(
				"text" => "Períodos",
				"nivel" => 3,
				"showMenu" => true 
			), /* end index */
			"nuevo" => array(
				"text" => "Nuevo",
				"nivel" => 4,
				"showMenu" => true	
			), /* end nuevo*/
			"editar" => array(
				"nivel" => 4,
				"showMenu" => false
			), /* end editar */
			"eliminar" => array(
				"nivel" => 4,
				"showMenu" => false
			) /* end eliminar */
		
		) /* end actions */
	), /* end periodos */
	
	"lugares" => array(
		"show" => true,
		"text" => "Lugares",
		"ico" => "controllers/lugares.png",
		"desc" => "Gestiona los lugares o espacios deportivos/culturales utilizados por Bienestar U",
		"nivel" => 2, ## nivel mínimo exigido por una acción del controlador
		"actions" => array(
		
			"index" => array(
				"text" => "Lugares",
				"nivel" => 2,
				"showMenu" => true
			), /*end index */
			"nuevo" => array(
				"text" => "Nuevo",
				"nivel" => 3,
				"showMenu" => true
			), /* end nuevo */
			"editar" => array(
				"nivel" => 3,
				"showMenu" => false
			), /* end editar */
			"eliminar" => array(
				"nivel" => 3,
				"showMenu" => false
			) /* end eliminar */
		
		) /* end actions */
	), /* end lugares */
	
	"usuarios" => array(
		"show" => true,
		"text" => "Usuarios",
		"ico" => "controllers/usuarios.png",
		"desc" => "Gestiona las cuentas de usuarios",
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