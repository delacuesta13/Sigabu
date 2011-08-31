<?php

/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$multidata = array(
	'dia' => array(
		'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'
	)
);

?>

<div class="form">

	<span style="color:#666;margin-bottom:2px">
		Actividad: <?php echo $data_horario[0]['Actividad']['nombre']?>
		(<?php echo $data_horario[0]['Area']['nombre']?>)<br/>
		Periodo: <?php echo $data_horario[0]['Periodo']['periodo']?>
	</span>
	
	<hr/>
	
	<div class="group navform wat-cf" style="margin-top:10px">
		<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $id . 'h/' . $id_curso . 'c/' . $actividad_url?>">
			<button class="button">
				<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar
			</button>
		</a>
	</div>
	
	<div class="columns wat-cf">	
		<div class="column left">
			<div class="group">
				<label class="label">Día</label>
				<?php echo $multidata['dia'][intval($data_horario[0]['Horario']['dia']) - 1]?>
			</div>
			<div class="group">
				<label class="label">Lugar</label>
				<?php echo $data_horario[0]['Lugar']['nombre']?>
			</div>
			<div class="group">
				<label class="label">Dirección</label>
				<?php echo $data_horario[0]['Lugar']['direccion']?>
			</div>
			<div class="group">
				<label class="label">Hora de Inicio</label>
				<?php echo $data_horario[0]['Horario']['hora_inic']?>
			</div>
			<div class="group">
				<label class="label">Hora de Finalización</label>
				<?php echo $data_horario[0]['Horario']['hora_fin']?>
			</div>
		</div>
		<div class="column right">
			<div class="group">	
				<label class="label">Comentario</label>
				<?php
				echo (strlen($data_horario[0]['Horario']['comentario'])!=0) ? ('<p>' . $data_horario[0]['Horario']['comentario'] . '</p>') : 'Ninguno';  
				?>
			</div>
		</div>
	</div>

</div>