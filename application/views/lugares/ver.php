<?php
/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
?>

<div class="form">

	<span style="color:#666;margin-bottom:2px">
		Nombre: <?php echo $data_lugar[0]['Lugar']['nombre']?>
	</span>
	<hr/>

	<div class="group navform wat-cf" style="margin-top:10px">
		<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $id . '/' . $nombre_url?>">
			<button class="button">
				<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar
			</button>
		</a>
	</div>
	
	<div class="columns wat-cf">	
		<div class="column left">
			<div class="group">
				<label class="label">Nombre</label>
				<?php echo $data_lugar[0]['Lugar']['nombre']?>
			</div>
			<div class="group">
				<label class="label">Administrador</label>
				<?php echo (strlen($data_lugar[0]['Lugar']['administrador'])==0) ? 'Ninguno' : $data_lugar[0]['Lugar']['administrador']?>
			</div>
			<div class="group">
				<label class="label">Dirección</label>
				<?php echo $data_lugar[0]['Lugar']['direccion']?>
			</div>
			<div class="group">
				<label class="label">Email</label>
				<?php echo (strlen($data_lugar[0]['Lugar']['email'])==0) ? 'Ninguno' : $data_lugar[0]['Lugar']['email']?>
			</div>
		</div>
		<div class="column right">
			<div class="group">
				<label class="label">Teléfono Fijo</label>
				<?php echo (strlen($data_lugar[0]['Lugar']['telefono_fijo'])==0) ? 'Ninguno' : $data_lugar[0]['Lugar']['telefono_fijo']?>
			</div>
			<div class="group">
				<label class="label">Teléfono Móvil</label>
				<?php echo (strlen($data_lugar[0]['Lugar']['telefono_movil'])==0) ? 'Ninguno' : $data_lugar[0]['Lugar']['telefono_movil']?>
			</div>
			<div class="group">
				<label class="label">Comentario</label>
				<?php echo (strlen($data_lugar[0]['Lugar']['comentario'])==0) ? 'Ninguno' : ('<p>' . $data_lugar[0]['Lugar']['comentario'] . '</p>')?>
			</div>
		</div>
	</div>

</div>