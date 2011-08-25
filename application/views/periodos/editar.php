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
			<sup>1</sup> El periodo debe de ser único.<br/>
			<sup>2</sup> Todos los campos son obligatorios.
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
				<p>Bueno, esto es vergonzoso. Se ha intentado editar el periodo, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 		
	}	
}

?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action . '/' . $id . '/' . $periodo_url?>">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="periodo">Período</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('periodo', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['periodo']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="periodo" id="periodo" maxlength="8" class="text_field"
				<?php 
				if(isset($_POST['periodo']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['periodo'] . '"';
				else echo 'value="' . $data_periodo[0]['Periodo']['periodo'] . '"';
				?>
				/>
				<span class="description">Ej: 2011-2</span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="fecha_inic">Fecha de Inicio</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('fecha_inic', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['fecha_inic']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="fecha_inic" id="fecha_inic" maxlength="10" class="text_field"
				<?php 
				if(isset($_POST['fecha_inic']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['fecha_inic'] . '"';
				else echo 'value="' . $data_periodo[0]['Periodo']['fecha_inic'] . '"';
				?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="fecha_fin">Fecha de Finalización</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('fecha_fin', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['fecha_fin']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="fecha_fin" id="fecha_fin" maxlength="10" class="text_field"
				<?php 
				if(isset($_POST['fecha_fin']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo 'value="' . $_POST['fecha_fin'] . '"';
				else echo 'value="' . $data_periodo[0]['Periodo']['fecha_fin'] . '"';				
				?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
			</div>	
		
		</div>
	</div>

	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar			
		</button>
	</div>	

</form>