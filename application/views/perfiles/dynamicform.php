<?php

if (isset($tipo_perfil)) {
	if (strtolower($tipo_perfil)=='estudiante') {
	?>
	
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="jornada">Jornada</label>
				<span class="error error_jornada"></span>
			</div>
			<select name="jornada" id="jornada" style="width:350px;">
				<option>Seleccione una Jornada</option>
				<?php 
				for($i = 0; $i < count($lista_jornadas); $i++){
					$str_salida = '<option value="' . $lista_jornadas[$i]['Multientidad']['id'] . '"';
					$str_salida .= '>' . $lista_jornadas[$i]['Multientidad']['nombre'] .'</option>';
					echo $str_salida;
				}
				?>
			</select>
		</div>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="programa">Programa Académico</label>
				<span class="error error_programa"></span>
			</div>	
			<select name="programa" id="programa" style="width:350px;">
				<option>Seleccione un Programa Académico</option>
				<?php 
				if(count($lista_programas)!=0){
					foreach ($lista_programas as $facultad => $opciones) {
					?>
						<optgroup label="<?php echo (strlen($opciones['nombre'])>15 && strlen($opciones['abrev'])!=0) ? $opciones['abrev'] : $opciones['nombre'];?>">
						<?php 
						$programas = $opciones['programas'];
						for ($i = 0; $i < count($programas); $i++) {
						?>
							<option value="<?php echo $programas[$i]['id']?>">
								<?php echo (strlen($programas[$i]['nombre'])>30 && strlen($programas[$i]['abrev'])!=0) ? $programas[$i]['abrev'] : $programas[$i]['nombre']?>
							</option>
						<?php 
						}							
						?>
						</optgroup>
					<?php 
					}
				}
				?>
			</select>		
		</div>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="semestre">Semestre</label>
				<span class="error error_semestre"></span>
			</div>
			<input type="text" name="semestre" id="semestre" maxlength="2" class="text_field"/>
			<span class="description">Ej: 10</span>
		</div>
		
	<?php 	
	} elseif (strtolower($tipo_perfil)=='docente') {
	?>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="contrato">Tipo de Contrato</label>
				<span class="error error_contrato"></span>
			</div>
			<select name="contrato" id="contrato" style="width:350px;">
				<option>Seleccione un Tipo de Contrato</option>
				<?php 
				for($i = 0; $i < count($lista_contratos); $i++){
					$str_salida = '<option value="' . $lista_contratos[$i]['Multientidad']['id'] . '"';
					$str_salida .= '>' . $lista_contratos[$i]['Multientidad']['nombre'] .'</option>';
					echo $str_salida;
				}
				?>
			</select>
		</div>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="programa">Programa Académico</label>
				<span class="error error_programa"></span>
			</div>	
			<select name="programa" id="programa" style="width:350px;">
				<option>Seleccione un Programa Académico</option>
				<?php 
				if(count($lista_programas)!=0){
					foreach ($lista_programas as $facultad => $opciones) {
					?>
						<optgroup label="<?php echo (strlen($opciones['nombre'])>15 && strlen($opciones['abrev'])!=0) ? $opciones['abrev'] : $opciones['nombre'];?>">
						<?php 
						$programas = $opciones['programas'];
						for ($i = 0; $i < count($programas); $i++) {
						?>
							<option value="<?php echo $programas[$i]['id']?>">
								<?php echo (strlen($programas[$i]['nombre'])>30 && strlen($programas[$i]['abrev'])!=0) ? $programas[$i]['abrev'] : $programas[$i]['nombre']?>
							</option>
						<?php 
						}							
						?>
						</optgroup>
					<?php 
					}
				}
				?>
			</select>		
		</div>
		
	<?php 	
	} elseif (strtolower($tipo_perfil)=='egresado') {
	?>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="programa">Programa Académico</label>
				<span class="error error_programa"></span>
			</div>	
			<select name="programa" id="programa" style="width:350px;">
				<option>Seleccione un Programa Académico</option>
				<?php 
				if(count($lista_programas)!=0){
					foreach ($lista_programas as $facultad => $opciones) {
					?>
						<optgroup label="<?php echo (strlen($opciones['nombre'])>15 && strlen($opciones['abrev'])!=0) ? $opciones['abrev'] : $opciones['nombre'];?>">
						<?php 
						$programas = $opciones['programas'];
						for ($i = 0; $i < count($programas); $i++) {
						?>
							<option value="<?php echo $programas[$i]['id']?>">
								<?php echo (strlen($programas[$i]['nombre'])>30 && strlen($programas[$i]['abrev'])!=0) ? $programas[$i]['abrev'] : $programas[$i]['nombre']?>
							</option>
						<?php 
						}							
						?>
						</optgroup>
					<?php 
					}
				}
				?>
			</select>		
		</div>
	
	<?php 	
	} elseif (strtolower($tipo_perfil)=='funcionario') {
	?>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="programa">Programa Académico<sup>*</sup></label>
				<span class="error error_programa"></span>
			</div>	
			<select name="programa" id="programa" style="width:350px;">
				<option>Seleccione un Programa Académico</option>
				<?php 
				if(count($lista_programas)!=0){
					foreach ($lista_programas as $facultad => $opciones) {
					?>
						<optgroup label="<?php echo (strlen($opciones['nombre'])>15 && strlen($opciones['abrev'])!=0) ? $opciones['abrev'] : $opciones['nombre'];?>">
						<?php 
						$programas = $opciones['programas'];
						for ($i = 0; $i < count($programas); $i++) {
						?>
							<option value="<?php echo $programas[$i]['id']?>">
								<?php echo (strlen($programas[$i]['nombre'])>30 && strlen($programas[$i]['abrev'])!=0) ? $programas[$i]['abrev'] : $programas[$i]['nombre']?>
							</option>
						<?php 
						}							
						?>
						</optgroup>
					<?php 
					}
				}
				?>
			</select>		
		</div>
		
		<hr/>
		
		<p>
			<span class="small">
				<sup>*</sup> Si no se indica programa académico,
				se infiere que el funcionario es administrativo.
			</span>
		</p>
		
	<?php 	
	} elseif (strtolower($tipo_perfil)=='familiar') {
	?>
	
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="parentesco">Tipo de Consanguineidad o Afinidad</label>
				<span class="error error_parentesco"></span>
			</div>
			<select name="parentesco" id="parentesco" style="width:350px;">
				<option>Seleccione un Tipo de Consanguineidad o Afinidad</option>
				<?php 
				for($i = 0; $i < count($lista_afinidad); $i++){
					$str_salida = '<option value="' . $lista_afinidad[$i]['Multientidad']['id'] . '"';
					$str_salida .= '>' . $lista_afinidad[$i]['Multientidad']['nombre'] .'</option>';
					echo $str_salida;
				}
				?>
			</select>
		</div>
		
		<div class="group">
			<div class="fieldWithErrors">
				<label class="label" for="apoderado">Identificación del Apoderado</label>
				<span class="error error_apoderado"></span>
			</div>
			<input type="text" name="apoderado" id="apoderado" maxlength="20" class="text_field"/>
			<span class="description">Ej: 1234567</span>
		</div>	
	
	<?php 	
	}
	
}