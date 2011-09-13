<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
 * Genero sidebar de Noticia
 */
$make_sidebar = '
<div class="block notice">
	<h4>Atención!</h4>
	<p>
		<sup>*</sup> Campos obligatorios.  
	</p>
</div>
';

$lista_meses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'); 
$ultima_visita = $_SESSION['ultima_visita'];

?>

<div class="form">

	<span style="color:#666;margin-bottom:2px">
		<?php echo $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos']?>
		(<?php echo $data_persona[0]['Persona']['tipo_dni'] . ' ' . $_SESSION['persona_dni']?>)<br/>
		Usuario: <?php echo $_SESSION['username']?><br/>
		Último ingreso: 
		<?php
		echo substr($ultima_visita, 8, 2) . ' ' . $lista_meses[intval(substr($ultima_visita, 5, 2)) - 1] . ' ' . substr($ultima_visita, 0,4) .
		' ' . substr($ultima_visita, 11, 5); 
		?>
	</span>

	<hr/>
	
	<div id="showMensaje" class="flash" style="display: none; margin-top: 15px"></div>
	
	<div class="columns wat-cf" style="margin-top: 10px">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="email">Email<sup>*</sup></label>
					<span class="error error_email"></span>
				</div>
				<input type="text" name="email" id="email" maxlength="60" class="text_field" value="<?php echo $email?>"/>
				<span class="description">Ej: nombre@correo.com</span>
			</div>
			
			<div class="group">
				<button class="button" id="btn_edit">
					<?php echo $html->includeImg('icons/password.png', 'edit_pass.png')?> Cambiar tu password			
				</button>
			</div>
			
			<div class="edit_pass" style="display: none;">
			
				<input type="hidden" id="edit_pass" value="0"/>
			
				<div class="group" style="margin-top: 55px;">
					<div class="fieldWithErrors">
						<label class="label" for="old_password">Password actual<sup>*</sup></label>
						<span class="error error_old_password"></span>
					</div>
					<input type="password" name="old_password" id="old_password" maxlength="45" class="text_field"/>				
				</div>
			
				<div class="group">
					<div class="fieldWithErrors">
						<label class="label" for="password">Nueva password<sup>*</sup></label>
						<span class="error error_password"></span>
					</div>
					<input type="password" name="password" id="password" title="Ingrese una password" maxlength="45" class="text_field"/>				
				</div>
			
				<div class="group">
					<div class="fieldWithErrors">
						<label class="label" for="confirm_password">Confirmar password<sup>*</sup></label>
						<span class="error error_confirm_password"></span>
					</div>
					<input type="password" name="confirm_password" id="confirm_password" maxlength="45" class="text_field"/>				
				</div>
			
			</div>
		
		</div>
	</div>
	
	<div class="group navform wat-cf" style="margin-top: 15px">
		<button class="button" id="btn_guardar">
			<?php echo $html->includeImg('icons/save.png', 'Guardar')?> Guardar			
		</button>
	</div>

</div>

<script type="text/JavaScript">
//<![CDATA[
           
$(function () {

	$("#btn_guardar").click(function() {

		var div_temp = '<div class="message"><p>';
		div_temp += '<?php echo $html->includeImg('ajax-loader.gif', 'Procesando')?>';
		div_temp += '</p></div>'; 
		
		$( "#showMensaje" ).html(div_temp);
		$( "#showMensaje" ).fadeIn("slow");

		var url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'editar_cuenta'?>';
		$.post(url, 
			{
				email: $("#email").val(), 
				edit_pass: $("#edit_pass").val(), 
				old_password: $("#old_password").val(), 
				password: $("#password").val(),
				confirm_password: $("#confirm_password").val()  
			},
			function(data) {
				$( "#showMensaje" ).html(data);
			}
		);
		
	});

});

//]]>
</script>