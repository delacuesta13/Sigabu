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
		<sup>1</sup> El nombre de usuario debe ser único.<br/><br/>
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
				<p>La cuenta de usuario se ha creado exitósamente.</p>
			</div>
		</div>
		<?php 		
	} else {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado crear la cuenta de usuario, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 
	}
	
}

?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action;?>">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="persona">Identificación de la Persona<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('persona', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['persona']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="persona" id="persona" maxlength="20" class="text_field"
				<?php if(isset($_POST['persona']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['persona'] . '"';?>
				/>
				<span class="description">Ej: 1234567</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="usuario">Usuario<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('usuario', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['usuario']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="usuario" id="usuario" maxlength="45" class="text_field" title="Ingrese un nombre de usuario."
				<?php if(isset($_POST['usuario']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['usuario'] . '"';?>
				/>				
				<span class="description">Ej: admin</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="password">Password<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('password', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['password']?></span>
					<?php
					} 
					?>
				</div>
				<input type="password" name="password" id="password" maxlength="45" class="text_field" title="Ingrese una password."
				<?php if(isset($_POST['password']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['password'] . '"';?>
				/>				
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="confirm_password">Confirmar Password<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('confirm_password', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['confirm_password']?></span>
					<?php
					} 
					?>
				</div>
				<input type="password" name="confirm_password" id="confirm_password" maxlength="45" class="text_field"
				<?php if(isset($_POST['confirm_password']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['confirm_password'] . '"';?>
				/>				
			</div>
			
		</div>
		<div class="column right">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="email">Email<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('email', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['email']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="email" id="email" maxlength="60" class="text_field"
				<?php if(isset($_POST['email']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['email'] . '"';?>
				/>
				<span class="description">Ej: nombre@correo.com</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="rol">Rol<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('rol', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['rol']?></span>
					<?php
					} 
					?>
				</div>
				<select name="rol" id="rol">
					<option>Seleccione un rol de usuario</option>
					<?php
					$str_select = '';
					for ($i = 0; $i < count($lista_roles); $i++) {
						$str_select .= '<option value="' . $lista_roles[$i]['Rol']['id'] . '"';
						if (isset($_POST['rol']) && (isset($ind_error) || (isset($rs_crear) && !rs_crear)) && $_POST['rol']==$lista_roles[$i]['Rol']['id'])
							$str_select .= ' selected="selected"';
						$str_select .= '>' . $lista_roles[$i]['Rol']['nombre'] . '</option>';
					} 
					echo $str_select;
					?>
				</select>
			</div>
			
			<div class="group">
				<input type="checkbox" name="estado" id="estado" class="checkbox"
				<?php 
				if (!isset($_POST['estado'], $ind_error, $rs_crear) || isset($_POST['estado'])) echo 'checked="checked"';
				?>
				/> <label for="estado" class="checkbox">Estado</label><br/>	
				<span class="description">Marque la casilla si la cuenta de usuario estará activa.</span>
			</div>
			
			<?php
			$showDiv = ''; 
			if (!isset($_POST['estado'], $ind_error, $rs_crear) || isset($_POST['estado']))
				$showDiv = 'style="display: none;"';
			?>
			<div class="group" id="div_fecha" <?php echo $showDiv;?>>
				<div class="fieldWithErrors">
					<label class="label" for="fecha_activacion">Fecha de Activación</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('fecha_activacion', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['fecha_activacion']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="fecha_activacion" id="fecha_activacion" maxlength="10" class="text_field"
				<?php if(isset($_POST['fecha_activacion']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['fecha_activacion'] . '"';?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
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