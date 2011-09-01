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
		'tipo_dni' => array(
			'CC' => 'Cédula de Ciudadanía',
			'CE' => 'Cédula de Extranjería',
			'TI' => 'Tarjeta de Identidad',
			'RC' => 'Registro Civil'
		),
		'genero' => array(
			'H' => 'Hombre (s)',
			'M' => 'Mujer (es)'
		),
		'flag' => array(
			'0' => $html->includeImg('icons/cross.png', 'No'),
			'1' => $html->includeImg('icons/tick.png', 'Sí')
		) 
	);
	include_once ROOT . DS . 'library/fechas.funciones.php';
	$array_meses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');

?>

<span style="color:#666;margin-bottom:2px">
	Actividad: <?php echo $data_programacion[0]['Actividad']['nombre']?>
	(<?php echo $data_programacion[0]['Area']['nombre']?>) <br/>
	Periodo: <?php echo $data_programacion[0]['Periodo']['periodo']?>
</span>

<hr/>

<div class="group navform wat-cf" style="margin-top:10px">
	<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $id . '/' . $actividad_url?>">
		<button class="button">
			<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar
		</button>
	</a>
</div>

<div id="tabs" style="margin-top:15px">

	<ul>
		<li><a href="#tabs-1">Información</a></li>
		<li><a href="#tabs-2">Inscripciones</a></li>
		<li><a href="#tabs-3">Horarios</a></li>
		<li><a href="#tabs-4">Asistencia</a></li>
	</ul>
	
	<!-- información -->
	<div id="tabs-1">
	
		<div class="form">
			
			<div class="columns wat-cf">
				<div class="column left">
					<div class="group">
						<label class="label">Monitor</label>
						<?php
						## existe monitor para la actividad 
						if (strlen($data_programacion[0]['Curso']['monitor_dni'])!=0) {
							$data_persona = performAction('personas', 'consultar_persona_fk', array($data_programacion[0]['Curso']['monitor_dni']));
							echo $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos'] . ' (' .
							'<span title="' . $multidata['tipo_dni'][$data_persona[0]['Persona']['tipo_dni']] . '" style="cursor: help">' . 
								$data_persona[0]['Persona']['tipo_dni'] . 
							'</span>' . ' ' . 
							$data_persona[0]['Persona']['dni'] . ')';
							unset($data_persona);							
						}
						?>
					</div>
					<div class="group">
						<label class="label">Fecha de Inicio</label>
						<?php 
						## ingresada fecha de inicio
						if (strlen($data_programacion[0]['Curso']['fecha_inic'])!=0) {
							echo substr($data_programacion[0]['Curso']['fecha_inic'], 8, 2) . ' ' .
							$array_meses[intval(substr($data_programacion[0]['Curso']['fecha_inic'], 5, 2)) - 1] . ' ' .
							substr($data_programacion[0]['Curso']['fecha_inic'], 0, 4);
						} else {
							echo 'Ninguna';
						}
						?>
					</div>
					<div class="group">
						<label class="label">Fecha de Finalización</label>
						<?php 
						## ingresada fecha de finalización
						if (strlen($data_programacion[0]['Curso']['fecha_fin'])!=0) {
							echo substr($data_programacion[0]['Curso']['fecha_fin'], 8, 2) . ' ' .
							$array_meses[intval(substr($data_programacion[0]['Curso']['fecha_fin'], 5, 2)) - 1] . ' ' .
							substr($data_programacion[0]['Curso']['fecha_fin'], 0, 4);
						} else {
							echo 'Ninguna';
						}
						?>
					</div>
				</div>
				<div class="column right">
					<div class="group">
						<label class="label">No. de Inscritos</label>
						<?php 
						## hay inscritos
						if (count($total_inscritos)!=0) {
							$str = '';
							$total = 0;
							for ($i = 0; $i < count($total_inscritos); $i++) {
								$total += $total_inscritos[$i]['']['inscritos'];
								$str .= $total_inscritos[$i]['']['inscritos'] . ' ' . $multidata['genero'][$total_inscritos[$i]['Persona']['genero']] . ', ';
							} /* for */
							$str =  $total . ' Persona (s) &#91;' . substr_replace($str, '', -2) . '&#93;';
							echo $str;
							unset ($str, $total);
						} else {
							echo '0 Personas';
						}
						?>
					</div>
					<div class="group">
						<label class="label">Comentario</label>
						<?php
						echo (strlen($data_programacion[0]['Curso']['comentario'])!=0) ? ('<p>' . $data_programacion[0]['Curso']['comentario'] . '</p>') : 'Ninguno';
						?>
					</div>
					<div class="group">
						<label class="label">Abierta</label>
						<?php 
						echo $multidata['flag'][$data_programacion[0]['Curso']['abierto']];
						?>
					</div>
				</div>
			</div>	
			
		</div>
	
	</div>
	
	<!-- inscripciones -->
	<div id="tabs-2">
	
		<div style="display: table-row;"> <!-- toolbar -->
			<div style="display: table-cell;vertical-align:middle;">
				<strong>Inscripciones de la Programación</strong>
			</div>
			<div style="display: table-cell; padding-left: 5px; vertical-align: middle;">
				<button class="button" id="btn_nuevo_inscripcion">
					<?php echo $html->includeImg('icons/add.png', 'Nuevo')?> Nuevo
				</button>
			</div>
		</div> <!-- end toolbar -->
		
		<div id="showMensaje-inscripciones" class="flash" style="display:none; margin-top:15px"></div>
			
		<div id="dynamic-inscripciones" style="padding-top: 15px;"> <!-- div donde cargo el ajax -->
		</div> <!-- end dynamic -->
		
	</div>
	
	<!-- horarios -->
	<div id="tabs-3">
		
		<div style="display: table-row;"> <!-- toolbar -->
			<div style="display: table-cell;vertical-align:middle;">
				<strong>Horarios de la Programación</strong>
			</div>
			<div style="display: table-cell; padding-left: 5px; vertical-align: middle;">
				<a href="<?php echo BASE_PATH . '/' . 'horarios' . '/' . 'nuevo' . '/' . $id . '/' . $actividad_url?>">
					<button class="button" id="btn_nuevo_horario">
						<?php echo $html->includeImg('icons/add.png', 'Nuevo')?> Nuevo
					</button>
				</a>
			</div>
		</div> <!-- end toolbar -->
		
		<div id="showMensaje-horarios" class="flash" style="display:none; margin-top:15px"></div>
			
		<div id="dynamic-horarios" style="padding-top: 15px;"> <!-- div donde cargo el ajax -->
		</div> <!-- end dynamic -->
		
	</div>
	
	<!-- asistencia -->
	<div id="tabs-4">
	
		<div style="display: table-row;"> <!-- toolbar -->
			<div style="display: table-cell;vertical-align:middle;">
				<strong>Asistencia de la Programación</strong>
			</div>
			<div style="display: table-cell; padding-left: 5px; vertical-align: middle;">
				<button class="button" id="btn_nuevo_asistencia">
					<?php echo $html->includeImg('icons/add.png', 'Nuevo')?> Nuevo
				</button>
			</div>
		</div> <!-- end toolbar -->
		
		<div id="showMensaje-asistencias" class="flash" style="display:none; margin-top:15px"></div>
			
		<div id="dynamic-asistencias" style="padding-top: 15px;"> <!-- div donde cargo el ajax -->
		</div> <!-- end dynamic -->
	
	</div>

</div>

<div id="dialog-confirm-inscripcion" title="Eliminar inscripcion" style="display: none;"></div>
<div id="dialog-confirm-asistencia" title="Eliminar asistencia" style="display: none;"></div>
<div id="dialog-confirm-horario" title="Eliminar horario" style="display: none;"></div>
<div id="dialog-nuevo-inscripcion" title="Nueva inscripción" style="display: none;"></div>