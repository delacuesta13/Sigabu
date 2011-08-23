<div class="flash" id="showMensaje_crear" style="display: none;"></div>

<form method="post" name="formulario" id="formulario" class="form" action="#">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<div class="fieldWithErrors">
					<label class="label" for="periodo">Periodo</label>
					<span class="error error_periodo"></span>
				</div>	
				<select name="periodo" id="periodo" style="width:350px;">
					<option>Seleccione un Periodo</option>
					<?php 
					if(count($lista_periodos)!=0){
						foreach ($lista_periodos as $year => $periodos) {
							?>
							<optgroup label="<?php echo $year?>">
							<?php 
							for ($i = 0; $i < count($periodos); $i++) {
								?>
								<option value="<?php echo $periodos[$i]['id']?>"><?php echo $periodos[$i]['periodo']?></option>
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
					<label class="label" for="perfil">Perfil</label>
					<span class="error error_perfil"></span>
				</div>	
				<select name="perfil" id="perfil" style="width:350px;">
					<option>Seleccione un Perfil</option>
					<?php 
					for($i = 0; $i < count($lista_perfiles); $i++){
						$str_salida = '<option value="' . $lista_perfiles[$i]['Multientidad']['id'] . '"';
						$str_salida .= '>' . $lista_perfiles[$i]['Multientidad']['nombre'] .'</option>';
						echo $str_salida;
					}
					?>
				</select>
			</div>
			
			<div id="custom_form">
			</div>
			
		</div>
	</div>

</form>

<script type="text/JavaScript">
//<![CDATA[
           
$(function() {

	function dynamicForm() {
		var info_preload = '<div id="info_preload" class="dataTablas_preload">Cargando...</div>';
		$( "#custom_form" ).html(info_preload);
		url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/dynamicform'?>';
		$.post(url, {'perfil': $( "#perfil" ).val()}, function(data){
			$( "#custom_form" ).html(data);
		});	
	}

	$( "#perfil" ).change(function(){
		dynamicForm();
	});

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).css('display', 'none'); });
	});

	$( "#formulario" ).submit(function() {

		url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/crear_perfiles'?>';

		var $inputs = $('#formulario :input');
		
		var values = {};
	    $inputs.each(function() {
	        values[this.name] = $(this).val();
	    });

	    var info_preload = '<div id="info_preload" class="dataTablas_preload">Procesando...</div>';
		$( "#showMensaje_crear" ).html(info_preload);
		
		$.post(url, 
				{
					'dni': '<?php echo $dni?>',
					'fields[]' : values
				}, 
				function(data) {
					$( "#showMensaje_crear" ).html(data);
					$( "#showMensaje_crear" ).fadeIn("slow");
		});
		
		return false;

	});
	
});

//]]>
</script>