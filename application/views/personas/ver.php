<?php 
	$multidata = array(
		'tipo_dni' => array(
			'CC' => 'C�dula de Ciudadan�a',
			'CE' => 'C�dula de Extranjer�a',
			'TI' => 'Tarjeta de Identidad',
			'RC' => 'Registro Civil'
		),
		'genero' => array(
			'H' => 'Hombre',
			'M' => 'Mujer'
		),
		'flag' => array(
			'0' => $html->includeImg('icons/cross.png', 'No'),
			'1' => $html->includeImg('icons/tick.png', 'S�')
		)
	);
	include_once ROOT . DS . 'library/fechas.funciones.php';
?>

<div class="form">
	
	<span style="color:#666;margin-bottom:2px">
		Nombre: <?php echo $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos']?>
	</span>
	<hr/>
	
	<div class="group navform wat-cf" style="margin-top:10px">
		<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $dni?>">
			<button class="button">
				<?php echo $html->includeImg('icons/edit.png', 'Editar')?> Editar
			</button>
		</a>
		<button class="button" id="btn_eliminar">
			<?php echo $html->includeImg('icons/cross.png', 'Eliminar')?> Eliminar
		</button>
	</div>
	
	<div class="columns wat-cf">	
		<div class="column left">
			<div class="group">
				<label class="label">Nombres</label>
				<?php echo $data_persona[0]['Persona']['nombres']?>
			</div>
			<div class="group">
				<label class="label">Apellidos</label>
				<?php echo $data_persona[0]['Persona']['apellidos']?>
			</div>
			<div class="group">
				<label class="label">G�nero</label>
				<?php echo $multidata['genero'][$data_persona[0]['Persona']['genero']]?>
			</div>
			<div class="group">
				<label class="label">Tipo de Identificaci�n</label>
				<?php echo $multidata['tipo_dni'][$data_persona[0]['Persona']['tipo_dni']]?>
			</div>
			<div class="group">
				<label class="label">Identificaci�n</label>
				<?php echo $data_persona[0]['Persona']['dni']?>
			</div>
			<div class="group">
				<label class="label">Fecha de Nacimiento</label>
				<?php 
				$edad = '';
				if (strlen($data_persona[0]['Persona']['fecha_nac'])==0) $edad = 'Ninguna';
				else{
					$edad = calcular_dif_fechas($data_persona[0]['Persona']['fecha_nac'], date('Y-m-d'));
					$edad = $data_persona[0]['Persona']['fecha_nac'] . ' (' . $edad['years'] . ')';
				}
				echo $edad;
				?>
			</div>			
		</div>
		<div class="column right">			
			<div class="group">
				<label class="label">Tel�fono Fijo</label>
				<?php echo (strlen($data_persona[0]['Persona']['telefono_fijo'])==0) ? 'Ninguno' : $data_persona[0]['Persona']['telefono_fijo']?>
			</div>
			<div class="group">
				<label class="label">Tel�fono M�vil</label>
				<?php echo (strlen($data_persona[0]['Persona']['telefono_movil'])==0) ? 'Ninguno' : $data_persona[0]['Persona']['telefono_movil']?>
			</div>
			<div class="group">
				<label class="label">Email</label>
				<?php echo (strlen($data_persona[0]['Persona']['email'])==0) ? 'Ninguno' : $data_persona[0]['Persona']['email']?>
			</div>
			<div class="group">
				<label class="label">Direcci�n de Residencia</label>
				<?php echo (strlen($data_persona[0]['Persona']['direccion_residencia'])==0) ? 'Ninguna' : $data_persona[0]['Persona']['direccion_residencia']?>
			</div>
			<div class="group">
				<label class="label">Monitor</label>
				<?php echo $multidata['flag'][$data_persona[0]['Persona']['monitor']]?>
			</div>
			<div class="group">
				<label class="label">Estado</label>
				<?php echo $multidata['flag'][$data_persona[0]['Persona']['estado']]?>
			</div>
		</div>
	</div>

</div>

<div id="dialog-confirm" title="Eliminar persona" style="display: none;"></div>

<script type="text/JavaScript">
//<![CDATA[
$(function() {	
	$( '#btn_eliminar' ).bind('click', function() {
		var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
		'�Est� seguro que desea eliminar permanentemente esta persona?</p>' + 
		'<p style="margin-left:40px">' + 
			'<?php echo $data_persona[0]['Persona']['nombres'] . ' ' . $data_persona[0]['Persona']['apellidos']?>' + 
			'<br/>' + '<?php echo $data_persona[0]['Persona']['tipo_dni']?>' + ' ' + 
			'<?php echo $data_persona[0]['Persona']['dni']?>' +
		'</p>';
		$( "#dialog-confirm" ).html(msj_confirm);
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			width: 500,
			height: 200,
			buttons: {
				"S�": function() {
					url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/'?>' + <?php echo $data_persona[0]['Persona']['dni']?>;
					window.location.href = url;
				},
				"No": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
});
//]]>
</script>