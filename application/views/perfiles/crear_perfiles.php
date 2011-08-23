<?php 

if(isset($ind_error) && is_array($ind_error) && count($ind_error)){
	?>
	<div class="message warning">
		<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
	</div>
	<script type="text/JavaScript">
	//<![CDATA[
	$(function() {
	<?php
	foreach ($ind_error as $field => $error) {
		?>
		$( ".error_<?php echo $field?>" ).html( "<?php echo $error?>" );
		<?php
		unset($fields_to_bd[$field]);
	}
	unset($field, $error);
	?>
	});
	//]]>
    </script>
	<?php 
}

## limpio los campos que no tienen errores, y que quizás ya tenían un mensaje anterior
if (count($fields_to_bd)!=0) {
	?>
	<script type="text/JavaScript">
	//<![CDATA[
	<?php 
	foreach ($fields_to_bd as $field => $error) {
		?>
		$( ".error_<?php echo $field?>" ).html( "" );
		<?php 
	}
	unset($field, $error);
	?>
	//]]>
    </script>
	<?php
}

## no se recibieron errores
if (isset($rs_crear)) {

	## se creó exitósamente
	if($rs_crear) {
	?>
		<div class="flash">
			<div class="message notice">
				<p>Se ha asignado el perfil exit&oacute;samente.</p>
			</div>
		</div>
		<?php 		
	} else {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado guardar el perfil, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 
	}
	
}

?>