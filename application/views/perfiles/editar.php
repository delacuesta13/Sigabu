<div class="flash" id="showMensaje_editar" style="display: none;"></div>

<form method="post" name="formulario_editar" id="formulario_editar" class="form" action="#">

	<div class="columns wat-cf">
		<div class="column left">
		
			<div class="group">
				<label class="label">Periodo</label>
				<?php echo $data_perfil[0]['Periodo']['periodo']?>
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
						if (strtolower($lista_perfiles[$i]['Multientidad']['nombre'])==$tipo_perfil)
							$str_salida .= ' selected="selected"';
						$str_salida .= '>' . $lista_perfiles[$i]['Multientidad']['nombre'] .'</option>';
						echo $str_salida;
					}
					?>
				</select>
			</div>
		
			<div id="custom_form_edit">
			</div>
		
		</div>
	</div>

</form>

<script type="text/JavaScript">
//<![CDATA[
           
$(document).ready(function() {

	function dynamicForm_edit() {
		var info_preload = '<div id="info_preload" class="dataTablas_preload">Cargando...</div>';
		$( "#custom_form_edit" ).html(info_preload);
		url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/dynamicform'?>';
		$.post(url, {'perfil': $( "#perfil" ).val()}, function(data){
			$( "#custom_form_edit" ).html(data);
		});	
	}

	dynamicForm_edit();

	$( "#perfil" ).change(function(){
		dynamicForm_edit();
	});

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).css('display', 'none'); });
	});

	$( "#formulario_editar" ).submit(function() {

		url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar_perfil'?>';

		var $inputs = $('#formulario_editar :input');

		var values_edit = {};
	    $inputs.each(function() {
	    	values_edit[this.name] = $(this).val();
	    });

	    var info_preload = '<div id="info_preload" class="dataTablas_preload">Procesando...</div>';
		$( "#showMensaje_editar" ).html(info_preload);
		$( "#showMensaje_editar" ).css('display', 'block');

		$.post(url, 
				{
					'id' : '<?php echo $id?>',
					'dni': '<?php echo $dni?>',
					'fields[]' : values_edit
				}, 
				function(data) {
					$( "#showMensaje_editar" ).html(data);
					$( "#showMensaje_ediat" ).fadeIn("slow");
		});

		return false;

	});
	
});

//]]>
</script>