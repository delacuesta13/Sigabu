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
				<p>Bueno, esto es vergonzoso. Se ha intentado editar la persona, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 		
	}	
}
?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action . '/' . $dni . '/' . $nombre_url?>">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="nombres">Nombres<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('nombres', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['nombres']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="nombres" id="nombres" maxlength="45" class="text_field"
				<?php 
				if(isset($_POST['nombres']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['nombres'] . '"';
				else echo 'value="' . $data_persona[0]['Persona']['nombres'] . '"';
				?>
				/>
				<span class="description">Ej: John</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="apellidos">Apellidos<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('apellidos', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['apellidos']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="apellidos" id="apellidos" maxlength="45" class="text_field"
				<?php 
				if(isset($_POST['apellidos']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['apellidos'] . '"';
				else echo 'value="' . $data_persona[0]['Persona']['apellidos'] . '"';
				?>
				/>
				<span class="description">Ej: Smith</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="genero">Género<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('genero', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['genero']?></span>
					<?php
					} 
					?>
				</div>
				<select name="genero" id="genero">
					<option>Seleccione</option>
					<option value="H" 
					<?php 
					if(isset($_POST['genero']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['genero']=="H") echo 'selected="selected"';
					elseif(strtoupper($data_persona[0]['Persona']['genero']) == "H") echo 'selected="selected"';
					?>
					>Hombre</option>
					<option value="M" 
					<?php 
					if(isset($_POST['genero']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['genero']=="M") echo 'selected="selected"';
					elseif(!isset($_POST['genero']) && strtoupper($data_persona[0]['Persona']['genero']) == "M") echo 'selected="selected"';
					?>
					>Mujer</option>
				</select>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="tipo_dni">Tipo de Identificación<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('tipo_dni', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['tipo_dni']?></span>
					<?php
					} 
					?>
				</div>
				<select name="tipo_dni" id="tipo_dni">
					<option>Seleccione un tipo de Identificación</option>
					<option value="CC" 
					<?php 
					if(isset($_POST['tipo_dni']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['tipo_dni'] == "CC") echo 'selected="selected"';
					elseif(!isset($_POST['tipo_dni']) && strtoupper($data_persona[0]['Persona']['tipo_dni']) == "CC") echo 'selected="selected"';
					?>
					>Cédula de Cuidadanía</option>
					<option value="CE" 
					<?php 
					if(isset($_POST['tipo_dni']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['tipo_dni']=="CE") echo 'selected="selected"';
					elseif(!isset($_POST['tipo_dni']) && strtoupper($data_persona[0]['Persona']['tipo_dni']) == "CE") echo 'selected="selected"';
					?>
					>Cédula de Extranjería</option>
					<option value="TI" 
					<?php 
					if(isset($_POST['tipo_dni']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['tipo_dni']=="TI") echo 'selected="selected"';
					elseif(!isset($_POST['tipo_dni']) && strtoupper($data_persona[0]['Persona']['tipo_dni']) == "TI") echo 'selected="selected"';
					?>
					>Tarjeta de Identidad</option>
					<option value="RC" 
					<?php 
					if(isset($_POST['tipo_dni']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['tipo_dni']=="RC") echo 'selected="selected"';
					elseif(!isset($_POST['tipo_dni']) && strtoupper($data_persona[0]['Persona']['tipo_dni']) == "RC") echo 'selected="selected"';
					?>
					>Registro Civil</option>
				</select>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="dni">Identificación<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('dni', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['dni']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="dni" id="dni" maxlength="20" class="text_field"
				<?php 
				if(isset($_POST['dni']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['dni'] . '"';
				else echo 'value="' . $data_persona[0]['Persona']['dni'] . '"';
				?>
				/>
				<span class="description">Ej: 1234567</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="fecha_nac">Fecha de Nacimiento</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('fecha_nac', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['fecha_nac']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="fecha_nac" id="fecha_nac" maxlength="10" class="text_field"
				<?php 
				if(isset($_POST['fecha_nac']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['fecha_nac'] . '"';
				elseif(strlen($data_persona[0]['Persona']['fecha_nac'])!=0 && $data_persona[0]['Persona']['fecha_nac']!='0000-00-00') 
					echo 'value="' . $data_persona[0]['Persona']['fecha_nac'] . '"';
				?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
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
				else echo 'value="' . $data_persona[0]['Persona']['telefono_fijo'] . '"';
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
				else echo 'value="' . $data_persona[0]['Persona']['telefono_movil'] . '"';
				?>
				/>
				<span class="description">Ej: 3004864444</span>
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
				else echo 'value="' . $data_persona[0]['Persona']['email'] . '"';
				?>
				/>
				<span class="description">Ej: nombre@correo.com</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="direccion_residencia">Dirección de Residencia</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('direccion_residencia', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['direccion_residencia']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="direccion_residencia" id="direccion_residencia" maxlength="60" class="text_field"
				<?php 
				if(isset($_POST['direccion_residencia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['direccion_residencia'] . '"';
				else echo 'value="' . $data_persona[0]['Persona']['direccion_residencia'] . '"';
				?>
				/>
				<span class="description">Ej: Cra 73 2A-80 Barrio Buenos Aires</span>
			</div>
			
			<div class="group">
				<input type="checkbox" name="monitor" id="monitor" class="checkbox"
				<?php 
				if(isset($_POST['monitor']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'checked="checked"';
				elseif($data_persona[0]['Persona']['monitor']=='1') echo 'checked="checked"';
				?>
				/> <label for="monitor" class="checkbox">Monitor</label><br/>	
				<span class="description">Activar si la persona está vinculada a Bienestar U como Monitor (a) y/o Entrenador (a).</span>
			</div>
			
			<div class="group">
				<input type="checkbox" name="estado" id="estado" class="checkbox"
				<?php 
				if(isset($_POST['estado']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'checked="checked"';
				elseif($data_persona[0]['Persona']['estado']=='1') echo 'checked="checked"';
				?>
				/> <label for="estado" class="checkbox">Estado</label><br/>	
				<span class="description">Activar si la persona está habilitada para ser beneficiaria de Bienestar U.</span>
			</div>
		
		</div>
	</div>
	
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar			
		</button>
	</div>	
	
</form>