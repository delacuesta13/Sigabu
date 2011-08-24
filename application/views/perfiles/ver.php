<div class="form">

	<div class="group">
		<label class="label">Periodo</label>
		<?php echo $data_perfil[0]['Periodo']['periodo']?>
	</div>
	
	<div class="group">
		<label class="label">Perfil</label>
		<?php echo $data_perfil[0]['Multientidad']['nombre']?>
	</div>
	
<?php
if ($tipo_perfil == 'estudiante') {
?>
	
	<div class="group">
		<label class="label">Facultad</label>
		<?php echo $data_query[0]['Facultad']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Programa Académico</label>
		<?php echo $data_query[0]['Programa']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Semestre</label>
		<?php echo $data_query[0]['Perfil']['semestre']?>
	</div>
	
	<div class="group">
		<label class="label">Jornada</label>
		<?php echo $data_query[0]['Multientidad']['nombre']?>
	</div>

<?php
} elseif ($tipo_perfil == 'docente') {
?>
	
	<div class="group">
		<label class="label">Facultad</label>
		<?php echo $data_query[0]['Facultad']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Programa Académico</label>
		<?php echo $data_query[0]['Programa']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Tipo de Contrato</label>
		<?php echo $data_query[0]['Multientidad']['nombre']?>
	</div>

<?php
} elseif ($tipo_perfil == 'egresado') {
?>

	<div class="group">
		<label class="label">Facultad</label>
		<?php echo $data_query[0]['Facultad']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Programa Académico</label>
		<?php echo $data_query[0]['Programa']['nombre']?>
	</div>
	
<?php 
} elseif ($tipo_perfil == 'familiar') {
?>
	
	<div class="group">
		<label class="label">Parentesco</label>
		<?php echo $data_query[0]['Multientidad']['nombre']?>
	</div>
	
	<div class="group">
		<label class="label">Apoderado</label>
		<?php echo $data_query[0]['Persona']['nombres'] . ' ' . $data_query[0]['Persona']['apellidos']?>
		(<?php echo $data_query[0]['Persona']['tipo_dni'] . ' ' . $data_query[0]['Persona']['dni']?>) 
	</div>

<?php 
} elseif ($tipo_perfil == 'funcionario' && isset($administrativo)) {
	
	## el funcionario no es administrativo, pertenece a un programa
	if (!$administrativo) {
	?>
		
		<div class="group">
			<label class="label">Facultad</label>
			<?php echo $data_query[0]['Facultad']['nombre']?>
		</div>
		
		<div class="group">
			<label class="label">Programa Académico</label>
			<?php echo $data_query[0]['Programa']['nombre']?>
		</div>
		
	<?php 	
	} else {
	?>
		
	<div class="group">
		<label class="label">Área</label>
		Administrativa
	</div>
	
	<?php 
	}
	
}
?>

</div>