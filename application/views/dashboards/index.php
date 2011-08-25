<?php 

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if(isset($showMensaje) && is_array($showMensaje) && count($showMensaje)!=0){
	?>
	<div class="flash">
		<div class="message <?php echo $showMensaje['tipo']?>">
			<p><?php echo $showMensaje['mensaje']?></p>
		</div>
	</div>
	<?php 
}
?>

<ul class="controllerslist">
<?php 

/**
 * Imprimir dashboard de controladores
 */

$menu_project = $GLOBALS['menu_project'];

foreach ($menu_project as $controlador => $opciones){
	
	/**
	 * mostrar el controlador si el usuario tiene el permiso suficiente
	 * para interactuar con éste, y si se ha definido el controlador para
	 * que sea mostrado en la dashboard
	 */
	if($_SESSION['nivel'] >= $opciones['nivel'] && ((array_key_exists('show', $opciones) && $opciones['show']) || !array_key_exists('show', $opciones))){		
		?>
		<li title="<?php echo $opciones['desc']?>">
			<?php 
			echo $html->link($html->includeImg($opciones['ico'], $controlador) . "\n<span>" . strtoupper((array_key_exists('text', $opciones)) ? $opciones['text'] : $controladores) . "</span>", $controlador . '/');
			?>
		</li>
		<?php		
	}
	
}

unset($menu_project, $controlador, $opciones);
?>
</ul>
