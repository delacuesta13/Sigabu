<?php

/*
* Genero sidebar de Noticia
*/
$make_sidebar = '
	<div class="block notice">
		<h4>Atención!</h4>
		<p>	
			<sup>1</sup> El nombre de la actividad debe ser único.<br/><br/>
			<sup>*</sup> Campos obligatorios.
		</p>
	</div>
';

## revisar si se reciben errores
if (isset($ind_error) && is_array($ind_error) && count($ind_error)!=0) {
	?>
<div class="flash">
	<div class="message warning">
		<p>Parte de la información es incorrecta. Corrija el formulario e inténtelo de nuevo.</p>
	</div>
</div>
<?php 	
}
## no se recibieron errores
elseif (isset($rs_crear)) {
	
	## se creó exitósamente
	if($rs_crear) {
		?>
		<div class="flash">
			<div class="message notice">
				<p>La actividad se ha creado exitósamente.</p>
			</div>
		</div>
		<?php 		
	} else {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado guardar la actividad, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 
	}
	
}

?>

<form method="post" name="formulario" id="formulario" action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action;?>" class="form">
	
	<div class="columns wat-cf">
		<div class="column left">
	
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="nombre">Nombre<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('nombre', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['nombre']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="nombre" id="nombre" maxlength="60" class="text_field"
				<?php if(isset($_POST['nombre']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['nombre'] . '"';?>
				/>
				<span class="description">Ej: Baloncesto</span>
			</div>
	
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="area">Área<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('area', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['area']?></span>
					<?php
					} 
					?>	
				</div>
				<select name="area" id="area" style="width:350px;">
					<option>Seleccione</option>
					<?php 
					for($i = 0; $i < count($lista_areas); $i++){
						$str_salida = '<option value="' . $lista_areas[$i]['Area']['id'] . '"';
						if(isset($_POST['area']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear)) && $_POST['area']==$lista_areas[$i]['Area']['id']){
							$str_salida .= ' selected="selected"';
						}
						$str_salida .= '>' . $lista_areas[$i]['Area']['nombre'] .'</option>';
						echo $str_salida;
					}
					?>
				</select>
			</div>
			
			<div class="group">
				<label class="label" for="comentario">Comentario</label>
				<textarea class="text_area" name="comentario" id="comentario" rows="4" cols="80"><?php if(isset($_POST['comentario']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo $_POST['comentario'];?></textarea>
			</div>
			
		</div>
	</div>
	
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/tick.png', 'Guardar')?> Guardar			
		</button>
		<span class="text_button_padding">o</span>
		<a class="text_button_padding link_button cancel" href="#">Cancelar</a>
	</div>

</form>