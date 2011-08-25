<?php 
/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
?>

<script type="text/JavaScript">
//<![CDATA[
$(document).ready(function() {
<?php 
if (isset($act_notfound) && $act_notfound) {
	?>
	$( "#showMensaje" ).html('<div class="message notice"><p>Vaya! No se ha encontrado la actividad.</p></div>');
	$( "#showMensaje" ).fadeIn("slow");
	load_dataTable (1, '', '', '', '');
	<?php 	
} else {
	?>
	$( '#btn_eliminar' ).bind('click', function() {
		var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
		'¿Está seguro que desea eliminar permanentemente esta actividad?</p>' + 
		'<p style="margin-left:40px">' + 
			'<?php echo $data_actividad[0]['Act']['nombre']?>' + '<br/>' + '<?php echo $data_actividad[0]['Area']['nombre']?>' +
		'</p>';
		$( "#dialog-confirm" ).html(msj_confirm);
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			width: 500,
			height: 200,
			buttons: {
				"Sí": function() {
					url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/'?>' + <?php echo $data_actividad[0]['Act']['id']?>;
					$.ajax({
						url: url,
						success: function(data) {
							$( "#showMensaje" ).html(data);
							$( "#showMensaje" ).fadeIn("slow");
							load_dataTable (1, '', '', '', '');
						}
					});
					$( this ).dialog( "close" );
				},
				"No": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
	<?php 	
}
?>
});
//]]>
</script>

<div class="form">

	<span style="color:#666;margin-bottom:2px">
		Nombre: <?php echo $data_actividad[0]['Act']['nombre']?>
	</span>
	<hr/>
	
	<div class="group navform wat-cf" style="margin-top:10px">
		<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $data_actividad[0]['Act']['id']?>">
			<button class="button">
				<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar
			</button>
		</a>
		<button class="button" id="btn_eliminar">
			<?php echo $html->includeImg('icons/cross.png', 'Eliminar')?> Eliminar
		</button>
	</div>
	
	<div class="columns wat-cf">	
		<div class="column left">
			<div class="group">
				<label class="label">Nombre</label>
				<?php echo $data_actividad[0]['Act']['nombre']?>
			</div>
			<div class="group">
				<label class="label">Área</label>
				<?php echo $data_actividad[0]['Area']['nombre']?>
			</div>
			<div class="group">
				<label class="label">Comentario</label>
				<?php echo(strlen($data_actividad[0]['Act']['comentario'])==0) ? 'Ninguno' : ('<p>' . $data_actividad[0]['Act']['comentario'] . '</p>')?>
			</div>
		</div>
	</div>

</div>

<div id="dialog-confirm" title="Eliminar actividad" style="display: none;"></div>