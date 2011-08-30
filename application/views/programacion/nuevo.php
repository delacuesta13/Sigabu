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
		<sup>1</sup> La persona a la cual se asignará como monitor, debe
		estar activa en sus campos de <i>Estado</i> y <i>Monitor</i>.<br/>
		<sup>2</sup> Las fechas (de inicio y finalización) deben estar
		dentro del rango de fechas de un determinado periodo.<br/><br/>
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
				<p>La programación se ha creado exitósamente.</p>
			</div>
		</div>
		<?php 		
	} else {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado guardar la programación, pero al parecer existe un error.</p>
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
					<label class="label" for="actividad">Actividad<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('actividad', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['actividad']?></span>
					<?php
					} 
					?>
				</div>
				<select name="actividad" id="actividad">
					<option>Seleccione una Actividad</option>
					<?php 
					if (count($lista_actividades)) {
						$str_select = '';
						foreach ($lista_actividades as $area => $actividades) {
							$str_select .= '<optgroup label="' . $area . '">';
							for ($i = 0; $i < count($actividades); $i++) {
								$str_select .= '<option value="' . $actividades[$i]['id'] . '"';
								if (isset($_POST['actividad']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))  && $_POST['actividad']==$actividades[$i]['id'])
									$str_select .= ' selected="selected"';
								$str_select .= '>';
								$str_select .= $actividades[$i]['nombre'];
								$str_select .= '</option>';
							} /* for */
							$str_select .= '</optgroup>';
						} /* foreach */
						echo $str_select;
						unset ($str_select, $area, $actividades);
					} /* if */
					?>
				</select>
			</div>
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="periodo">Periodo<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('periodo', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['periodo']?></span>
					<?php
					} 
					?>
				</div>
				<select name="periodo" id="periodo">
					<option>Seleccione un Periodo</option>
					<?php 
					if (count($lista_periodos)!=0) {
						$str_select = '';						
						foreach ($lista_periodos as $year => $periodos) {
							$str_select .= '<optgroup label="' . $year . '">';
							for ($i = 0; $i < count($periodos); $i++) {
								$str_select .= '<option value="' . $periodos[$i]['id'] . '"';
								## default periodo si ya se envío el formulario y se reciben errores o no se puede crear
								if (isset($_POST['periodo']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear)) && $_POST['periodo']==$periodos[$i]['id'])
									$str_select .= ' selected="selected"';
								$str_select .= '>';
								$str_select .= $periodos[$i]['periodo']; 
								$str_select .= '</option>'; 
							} /* for */	
							$str_select .= '</optgroup>';						
						} /* foreach */	
						echo $str_select;
						unset ($str_select, $year, $periodos);					
					} /* if */
					?>
				</select>
			</div>
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="monitor">Identificación del Monitor</label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('monitor', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['monitor']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="monitor" id="monitor" maxlength="20" class="text_field"
				<?php if(isset($_POST['monitor']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['monitor'] . '"';?>
				/>
				<span class="description">Ej: 1234567</span>
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
				<?php if(isset($_POST['fecha_inic']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['fecha_inic'] . '"';?>
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
				<?php if(isset($_POST['fecha_fin']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['fecha_fin'] . '"';?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
			</div>		
		
		</div>
		<div class="column right">
		
			<div class="group">
				<label class="label" for="comentario">Comentario</label>
				<textarea class="text_area" name="comentario" id="comentario" rows="4" cols="80"><?php if(isset($_POST['comentario']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo $_POST['comentario'];?></textarea>
			</div>
			
			<div class="group">
				<input type="checkbox" name="abierto" id="abierto" class="checkbox"
				<?php 
				if(isset($_POST['abierto']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'checked="checked"';
				elseif(!isset($_POST['abierto']) && !isset($ind_error) && !isset($rs_crear)) echo 'checked="checked"';
				?>
				/> <label for="abierto" class="checkbox">Abierta</label><br/>	
				<span class="description">Para más información presione <a href="JavaScript:void(0);" onclick="showInfo();">aquí</a>.</span>
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

<div id="info_programacion_abierta" title="Información" style="display: none">
	<p>
		Si activa la casilla <i>abierta</i>, las inscripciones
		de la programación de la actividad serán abiertas o públicas,
		es decir, las personas de la comunidad universitaria se inscribirán
		a ésta directamente.
	</p>
	<p>
	En caso contrario, las inscripciones de la programación de la actividad sólo
	las podrán realizar el personal autorizado de Bienestar U que interactúa con este
	sistema.
	</p>
</div>