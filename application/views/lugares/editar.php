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
			<sup>1</sup> El nombre del lugar debe ser único.<br/><br/>
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
elseif (isset($rs_editar)) {	
	## se creó exitósamente
	if(!$rs_editar) {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado editar el lugar, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 		
	}	
}
?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action . '/' . $id . '/' . $nombre_url?>">

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
				<?php 
				if(isset($_POST['nombre']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['nombre'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['nombre'] . '"';
				?>
				/>
				<span class="description">Ej: Estadio Olímpico Pascual Guerrero</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="administrador">Administrador</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('administrador', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['administrador']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="administrador" id="administrador" maxlength="80" class="text_field"
				<?php 
				if(isset($_POST['administrador']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['administrador'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['administrador'] . '"';
				?>
				/>
				<span class="description">Ej: John Doe</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="direccion">Dirección<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('direccion', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['direccion']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="direccion" id="direccion" maxlength="60" class="text_field"
				<?php 
				if(isset($_POST['direccion']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['direccion'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['direccion'] . '"';
				?>
				/>
				<span class="description">Ej: Cra 73 2A-80 Barrio Buenos Aires</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="email">Email</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('email', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['email']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="email" id="email" maxlength="60" class="text_field"
				<?php 
				if(isset($_POST['email']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['email'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['email'] . '"';
				?>
				/>
				<span class="description">Ej: nombre@correo.com</span>
			</div>		
		
		</div>
		<div class="column right">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="telefono_fijo">Teléfono Fijo</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('telefono_fijo', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['telefono_fijo']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="telefono_fijo" id="telefono_fijo" maxlength="45" class="text_field"
				<?php 
				if(isset($_POST['telefono_fijo']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['telefono_fijo'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['telefono_fijo'] . '"';
				?>
				/>
				<span class="description">Ej: 4864444</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="telefono_movil">Teléfono Móvil</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('telefono_movil', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['telefono_movil']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="telefono_movil" id="telefono_movil" maxlength="45" class="text_field"
				<?php 
				if(isset($_POST['telefono_movil']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['telefono_movil'] . '"';
				else echo 'value="' . $data_lugar[0]['Lugar']['telefono_movil'] . '"';
				?>
				/>
				<span class="description">Ej: 3004864444</span>
			</div>
			
			<div class="group">
				<label class="label" for="comentario">Comentario</label>
				<textarea class="text_area" name="comentario" id="comentario" rows="4" cols="80"><?php if(isset($_POST['comentario']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo $_POST['comentario']; else echo $data_lugar[0]['Lugar']['comentario'];?></textarea>
			</div>
		
		</div>
	</div>
	
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar			
		</button>
	</div>

</form>