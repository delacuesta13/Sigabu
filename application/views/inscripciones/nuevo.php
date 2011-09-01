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

<div class="flash" id="showMensaje_nuevo_inscripcion" style="display: none; cursor: pointer;"></div>

<form method="post" name="formulario-inscripcion" id="formulario-inscripcion" class="form" action="#">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="persona">Identificación de la Persona</label>
					<span class="error error_persona"></span>
				</div>
				<input type="text" name="persona" id="persona" maxlength="20" class="text_field"/>
				<span class="description">Ej: 1234567</span>
			</div>	
		
		</div>
	</div>

</form>

<script type="text/JavaScript">
//<![CDATA[

$(function () {

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).css('display', 'none'); });
	});

	$("#formulario-inscripcion").submit(function() {

		url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/crear_inscripcion'?>';

		var info_preload = '<div id="info_preload" class="dataTablas_preload">Procesando...</div>';
		$( "#showMensaje_nuevo_inscripcion" ).html(info_preload);
		$( "#showMensaje_nuevo_inscripcion" ).fadeIn("slow");

		$.post(url, 
				{
					'curso': '<?php echo $id_curso?>',
					'persona' : $("#persona").val()
				}, 
				function(data) {
					$( "#showMensaje_nuevo_inscripcion" ).html(data);
					$( "#showMensaje_nuevo_inscripcion" ).fadeIn("slow");
		});
				
		return false;
		
	});
	
});

//]]>
</script>