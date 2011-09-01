<?php

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if(isset($ind_error) && is_array($ind_error) && count($ind_error)){
	?>
	<div class="message warning">
		<p>Parte de la informaci�n es incorrecta. Corrija el formulario e int�ntelo de nuevo.</p>
	</div>
	<script type="text/JavaScript">
	//<![CDATA[
	$(function() {
		$( ".error_persona" ).html( '<?php echo $ind_error['persona']?>' );
	});
	//]]>
    </script>
	<?php 
}

## limpio los campos que no tienen errores, y que quiz�s ya ten�an un mensaje anterior
else {
	?>
	<script type="text/JavaScript">
	//<![CDATA[
	$(function() {
		$( ".error_persona" ).html( '' );
	});
	//]]>
    </script>
	<?php
}

## no se recibieron errores
if (isset($rs_crear)) {

	## se cre� exit�samente
	if($rs_crear) {
	?>
		<div class="flash">
			<div class="message notice">
				<p>Se ha creado la inscripci&oacute;n exit&oacute;samente.</p>
			</div>
		</div>
		<?php 		
	} else {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado guardar la inscripci&oacute;n, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 
	}
	
}

?>