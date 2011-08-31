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
		<sup>1</sup> Los horarios de una programación, de un mismo día,
		no pueden cruzarse entre sí.<br/>
		<sup>2</sup> El formato de las horas debe ser de 24 horas.<br/><br/>
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
				<p>Bueno, esto es vergonzoso. Se ha intentado editar el horario, pero al parecer existe un error.</p>
			</div>
		</div>
		<?php 		
	}	
}

?>

<form method="post" name="formulario" id="formulario" class="form"
action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . $this->_action . '/' . $id . 'h/' . $id_curso . 'c/' . $actividad_url?>">

	<span style="color:#666;margin-bottom:2px">
		Actividad: <?php echo $data_horario[0]['Actividad']['nombre']?>
		(<?php echo $data_horario[0]['Area']['nombre']?>)<br/>
		Periodo: <?php echo $data_horario[0]['Periodo']['periodo']?>
	</span>
	
	<hr/>
	
	<div class="columns wat-cf" style="margin-top: 15px;">
		<div class="column left">
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="dia">Día<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('dia', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['dia']?></span>
					<?php
					} 
					?>
				</div>
				<select name="dia" id="dia">
					<option>Seleccione un día</option>
					<option value="1" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="1") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="1")
						echo 'selected="selected"';
					?>
					>Lunes</option>
					<option value="2" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="2") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="2")
						echo 'selected="selected"';
					?>
					>Martes</option>
					<option value="3" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="3") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="3")
						echo 'selected="selected"';
					?>
					>Miércoles</option>
					<option value="4" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="4") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="4")
						echo 'selected="selected"';
					?>
					>Jueves</option>
					<option value="5" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="5") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="5")
						echo 'selected="selected"';
					?>
					>Viernes</option>
					<option value="6" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="6") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="6")
						echo 'selected="selected"';
					?>
					>Sábado</option>
					<option value="7" 
					<?php 
					if(isset($_POST['dia']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['dia']=="7") 
						echo 'selected="selected"';
					elseif (!isset($_POST['dia']) && $data_horario[0]['Horario']['dia']=="7")
						echo 'selected="selected"';
					?>
					>Domingo</option>
				</select>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="lugar">Lugar<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('lugar', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['lugar']?></span>
					<?php
					} 
					?>	
				</div>
				<select name="lugar" id="lugar">
					<option>Seleccione un Lugar</option>
					<?php 
					for($i = 0; $i < count($lista_lugares); $i++){
						$str_salida = '<option value="' . $lista_lugares[$i]['Lugar']['id'] . '"';
						if(isset($_POST['lugar']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar)) && $_POST['lugar']==$lista_lugares[$i]['Lugar']['id']){
							$str_salida .= ' selected="selected"';
						} elseif(!isset($_POST['lugar']) && $data_horario[0]['Lugar']['id']==$lista_lugares[$i]['Lugar']['id']) {
							$str_salida .= ' selected="selected"';
						}
						$str_salida .= '>' . $lista_lugares[$i]['Lugar']['nombre'] .'</option>';
						echo $str_salida;
					}
					?>
				</select>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="hora_inic">Hora de Inicio<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('hora_inic', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['hora_inic']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="hora_inic" id="hora_inic" maxlength="5" class="text_field"
				<?php 
				if(isset($_POST['hora_inic']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) 
					echo 'value="' . $_POST['hora_inic'] . '"';
				else 	
					echo 'value="' . substr($data_horario[0]['Horario']['hora_inic'], 0, 5) . '"';
				?>
				/>
				<span class="description">Ej: <?php echo date('H:i')?></span>
			</div>
			
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="hora_fin">Hora de Finalización<sup>*</sup></label>
					<?php
					if(isset($ind_error) && is_array($ind_error) && array_key_exists('hora_fin', $ind_error)) {
					?>
					<span class="error"><?php echo $ind_error['hora_fin']?></span>
					<?php
					} 
					?>
				</div>
				<input type="text" name="hora_fin" id="hora_fin" maxlength="5" class="text_field"
				<?php 
				if(isset($_POST['hora_fin']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) 
					echo 'value="' . $_POST['hora_fin'] . '"';
				else
					echo 'value="' . substr($data_horario[0]['Horario']['hora_fin'], 0, 5) . '"';
				?>
				/>
				<span class="description">Ej: <?php echo date('H:i')?></span>
			</div>	
		
		</div>
		<div class="column right">
		
			<div class="group">
				<label class="label" for="comentario">Comentario</label>
				<textarea class="text_area" name="comentario" id="comentario" rows="4" cols="80"><?php if(isset($_POST['comentario']) && (isset($ind_error) || (isset($rs_editar) && !$rs_editar))) echo $_POST['comentario']; else echo $data_horario[0]['Horario']['comentario'];?></textarea>
			</div>
		
		</div>	
	</div>
	
	<div class="group navform wat-cf">
		<button class="button" type="submit">
			<?php echo $html->includeImg('icons/edit.png', 'Editarr')?> Editar			
		</button>
	</div>

</form>