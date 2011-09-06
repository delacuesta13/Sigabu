<?php
 
/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$lista_meses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');
$lista_dias = array('Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo');

?>

<script type="text/JavaScript">
//<![CDATA[
$(function() {

	$( "#regpag_asistencia" ).change(function() {
		load_dataTable ('asistencia', 1, $( "#regpag_asistencia" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_asistencia" ).val());
		
	});

	$( ".buscar_asistencia" ).bind("click", function() {
		load_dataTable ('asistencia', 1, $( "#regpag_asistencia" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_asistencia" ).val());
	});	

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).css('display', 'none'); });
	});

});
//]]>
</script>

<form action="#" method="get" class="form" style="margin-bottom:15px">
	<div class="columns wat-cf">
		<div class="column left">
			<label class="label">Mostrar</label>
			<select id="regpag_asistencia">
				<option value="10" 
				<?php if($record == 10) echo 'selected="selected"'?>
				>10</option>
				<option value="20"
				<?php if($record == 20) echo 'selected="selected"'?>
				>20</option>
				<option value="50"
				<?php if($record == 50) echo 'selected="selected"'?>
				>50</option>
				<option value="100"
				<?php if($record == 100) echo 'selected="selected"'?>
				>100</option>
			</select>
			registros por página
		</div>
		<div class="column right">
			<label class="label">Buscar</label>
			<input type="text" id="search_asistencia" size="45" 
			<?php if(isset($search)) echo 'value="' . $search . '"';?>		
			/>
			<span class="buscar_asistencia"><?php echo $html->includeImg('icons/search.png', 'Buscar')?></span>
		</div>
	</div>
</form>

<form method="post" name="formulario_eliminar_asistencia" id="formulario_eliminar_asistencia" action="#" class="form">
	<table class="table">
		<tr> <!-- cabecera -->
			<th class="first"><input type="checkbox" class="checkbox toggle" /></th>
			<?php 
			foreach ($campos_tabla as $tabla => $def) {
				if (array_key_exists('table', $def) && $def['table']) {
					foreach ($def['fields'] as $field => $attr) {
						if ($attr['showTable']) {
						?>
						<th class="<?php echo $def['alias'] . '_' . $field?>">
						<?php 
						echo (($attr['sort']) ? ((strtolower($def['alias'] . '.' . $field) == strtolower($sort)) ? ((strtolower($order) == 'asc') ?
								('<a onclick="load_dataTable(\'asistencia\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'desc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('asc.png', 'Ascendente') . '</a>')	:
								('<a onclick="load_dataTable(\'asistencia\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) :
							('<a onclick="load_dataTable(\'asistencia\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . '</a>')) :
						($attr['text']));
						?>
						</th>
						<?php
						} /* if */
					} /* foreach */
					unset($field, $attr);
				} /* if */
			} /* foreach */
			unset($tabla, $def);
			?>
		</tr> <!-- end cabecera -->
		<?php 
		for ($i = 0; $i < count($data_query); $i++) {
		?>
		<tr class="<?php echo (($i+1)%2==0) ? 'even' : 'odd' ?>"> <!-- cuerpo -->
			<td>
				<input type="checkbox" class="checkbox" id="id_asistencia" name="id_asistencia[]" value="<?php echo $data_query[$i]['Asistencia']['id']?>"/>
			</td>
			<td>
				<?php 
				$asistencia_text = $data_query[$i]['Asistencia']['fecha_asistencia'];
				$asistencia_text = explode ('-', $asistencia_text);
				$asistencia_text = $asistencia_text[2] . ' ' . $lista_meses[intval($asistencia_text[1]) - 1] . ' ' . $asistencia_text[0];
				?>
				<span title="<?php echo $asistencia_text?>">
					<?php echo $data_query[$i]['Asistencia']['fecha_asistencia']?>
				</span>
			</td>
			<td>
				<span title="<?php echo $data_query[$i]['Lugar']['nombre']?>">
					<?php 
					echo $lista_dias[intval($data_query[$i]['Horario']['dia']) - 1] . ' ' . substr($data_query[$i]['Horario']['hora_inic'], 0, 5) . ' - ' . 
					substr($data_query[$i]['Horario']['hora_fin'], 0, 5);
					?>
				</span>
			</td>
			<td><?php echo $data_query[$i]['Persona']['dni']?></td>
			<td><?php echo $data_query[$i]['Persona']['nombres']?></td>
			<td><?php echo $data_query[$i]['Persona']['apellidos']?></td>
		</tr>
		<?php 	
		} /* for */
		## no se encontraron registros
		if (count($data_query)==0) {
		?>
		<tr> <!-- cuerpo -->
			<td colspan="10" style="text-align: center;">Vaya! No se encontraron registros.</td>
		</tr> <!-- end cuerpo -->
		<?php	
		} /* if */
		?>
	</table>
	<div class="actions-bar wat-cf">
		<div class="actions">
			<button class="button" type="submit">
				<?php echo $html->includeImg('icons/cross.png', 'Eliminar')?> Eliminar
			</button>
		</div>
		<?php 
		## número de páginas
		$numpag = ceil($totalreg_query / $record);
		$numpag_antes = 3;
		$numpag_desp = 3;
		
		if ($numpag > 1) {
		?>	
		<div class="pagination">
			<?php 
			
			## número de la primera página
			$pag_inicio = (($pagina <= $numpag_antes) ? 1 : ($pagina - $numpag_antes));
			## número de la última página
			$pag_ultima = ((($pagina + $numpag_desp) > $numpag) ? $numpag : ($pagina + $numpag_desp));
				
			$q = ((isset($search)) ? $search : '');
			
			## imprimo paginación
			for ($i = $pag_inicio; $i <= $pag_ultima; $i++) {
				
				if ($i==$pag_inicio && $pagina==1) {
					echo '<span class="disabled prev_page">« Anterior</span>';
				} elseif ($i==$pag_inicio && $pagina!=1) {
					echo '<a rel="prev" class="prev_page" onclick="load_dataTable(\'asistencia\',' . ($pagina - 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">« Anterior</a>';
				}
				
				if ($i==$pagina) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a onclick="load_dataTable(\'asistencia\',' . $i . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">' . $i . '</a>';
				}
				
				if ($i==$pag_ultima && $pagina==$numpag) {
					echo '<span class="disabled next_page">Siguiente »</span>';
				} elseif ($i==$pag_ultima && $pagina!=$numpag) {
					echo '<a rel="next" class="next_page" onclick="load_dataTable(\'asistencia\',' . ($pagina + 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">Siguiente »</a>';
				}
				
			}
			?>
		</div>
		<?php	
		}		
		?>
	</div>
</form>

<script type="text/JavaScript">
//<![CDATA[
$(function (){
	$( "#formulario_eliminar_asistencia" ).submit(function() {

		// no seleccionó ninguna asistencia
		if ($("input[@id="+id_asistencia+"]:checked").length == 0) {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'No se ha seleccionado ninguna asistencia.</p>';
			$( "#dialog-confirm-asistencia" ).html(msj_confirm);
			$( "#dialog-confirm-asistencia" ).dialog({
				resizable: false,
				width: 500,
				height: 160,
				buttons: {
					"Ok": function() {
						$( this ).dialog( "close" );
					}
				}
			});
			return false;	
		} else {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'¿Está seguro que desea eliminar permanentemente esta (s) asistencia (s)?</p>';
			$( "#dialog-confirm-asistencia" ).html(msj_confirm);
			$( "#dialog-confirm-asistencia" ).dialog({
				resizable: false,
				width: 500,
				height: 160,
				buttons: {
					"Sí": function() {
						/* recorro los checkbox marcados y los agrego al array que enviaré */
						var data_id = new Array();
						$("input:checkbox[id=id_asistencia]:checked").each(function(){
							data_id.push($(this).val());	   
						});
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/' . $id_curso?>';
						$.post(url, {'id[]': data_id}, function(data){
							$( "#showMensaje-asistencia" ).html(data);
							$( "#showMensaje-asistencia" ).fadeIn("slow");
							load_dataTable ('asistencia', 1, $( "#regpag_asistencia" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_asistencia" ).val());
						});	
						$( this ).dialog( "close" );
					},
					"No": function() {
						$( this ).dialog( "close" );
					}
				}
			});	
			return false;
		}
		
	});
});
//]]>
</script>