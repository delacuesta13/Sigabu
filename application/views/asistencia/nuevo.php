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
elseif (isset($rs_crear)) {
	
	## NO se creó
	if(!$rs_crear) {
		?>
		<div class="flash">
			<div class="message error">
				<p>Bueno, esto es vergonzoso. Se ha intentado guardar la asistencia, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 		
	}
	
}

?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action . '/' . $id_curso . '/' . $actividad_url;?>">

	<span style="color:#666;margin-bottom:2px">
		Actividad: <?php echo $data_curso[0]['Actividad']['nombre']?>
		(<?php echo $data_curso[0]['Area']['nombre']?>)<br/>
		Periodo: <?php echo $data_curso[0]['Periodo']['periodo']?>
	</span>
	
	<hr/>
	
	<div class="columns wat-cf" style="margin-top: 15px;">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="fecha_asistencia">Fecha de Asistencia<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('fecha_asistencia', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['fecha_asistencia']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="fecha_asistencia" id="fecha_asistencia" maxlength="10" class="text_field"
				<?php if(isset($_POST['fecha_asistencia']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) echo 'value="' . $_POST['fecha_asistencia'] . '"';?>
				/>
				<span class="description">Ej: <?php echo date('Y-m-d');?></span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="horario">Horario<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('horario', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['horario']?></span>
					<?php
					} 
					?>
				</div>
				<select name="horario" id="horario">
					<option>Seleccione un Horario</option>
					<?php 
					## el curso tiene horarios
					if (count($lista_horarios)!=0) {
						$str_select = '';
						foreach ($lista_horarios as $dia => $horarios) {
							$str_select .= '<optgroup label="' . $dia . '">';
							for ($i = 0; $i < count($horarios); $i++) {
								$str_select .= '<option value="' . $horarios[$i]['id'] . '"';
								## seleccionar una opción por default
								if (isset($_POST['horario']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear)) && $_POST['horario']==$horarios[$i]['id'])
									$str_select .= ' selected="selected"';
								$str_select .= '>';
								$str_select .= substr($horarios[$i]['hora_inic'], 0, 5) . ' - ' . substr($horarios[$i]['hora_fin'], 0, 5);
								$str_select .= ' &#91;';
								$str_select .= ((strlen($horarios[$i]['lugar'])>40) ? 
									(rtrim(substr($horarios[$i]['lugar'], 0, 20)) . '... ' . ltrim(substr($horarios[$i]['lugar'], -17))) : 
									($horarios[$i]['lugar']));
								$str_select .= '&#93;';
								$str_select .= '</option>';
							} /* for */
							$str_select .= '</optgroup>';
						} /* foreach */
						echo $str_select;
						unset ($dia, $horarios);						
					} /* if */
					?>
				</select>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="personas">Personas<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('personas', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['personas']?></span>
					<?php
					} 
					?>
				</div>
				<select multiple="multiple" name="personas[]" id="personas" style="width:456px" data-placeholder="Seleccione las Personas de la Asistencia">
					<?php 
					$str_select = '';
					for ($i = 0; $i < count($lista_inscripciones); $i++) {
						$str_select .= '<option value="' . $lista_inscripciones[$i]['Persona']['dni'] . '"';
						if (isset($_POST['personas']) && (isset($ind_error) || (isset($rs_crear) && !$rs_crear))) {
							for ($j = 0; $j < count($_POST['personas']); $j++) {
								if ($_POST['personas'][$j]==$lista_inscripciones[$i]['Persona']['dni']) {
									$str_select .= ' selected = "selected"';
									break;
								}
							} /* for */
						} /* if */
						$str_select .= '>';
						$str_select .= $lista_inscripciones[$i]['Persona']['nombres'] . ' ' . $lista_inscripciones[$i]['Persona']['apellidos'];
						$str_select .= '</option>'; 
					} /* for */
					echo $str_select;
					?>
				</select>
			</div>
			
		</div>
	</div>
	
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/tick.png', 'Guardar')?> Guardar			
		</button>
	</div>
	

</form>