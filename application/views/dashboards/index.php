<?php 
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
	
	if($_SESSION['nivel'] >= $opciones['nivel']){		
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
