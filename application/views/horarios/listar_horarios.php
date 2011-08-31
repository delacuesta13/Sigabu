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
		'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes' , 'Sábado', 'Domingo'
	)
);

?>

<script type="text/JavaScript">
//<![CDATA[
$(function() {

	$( "#regpag_horarios" ).change(function() {
		load_dataTable ('horarios', 1, $( "#regpag_horarios" ).val(), '<?php echo $sort?>', 'asc', $( "#search_horarios" ).val());
		
	});

	$( ".buscar_horarios" ).bind("click", function() {
		load_dataTable ('horarios', 1, $( "#regpag_horarios" ).val(), '<?php echo $sort?>', 'asc', $( "#search_horarios" ).val());
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
			<select id="regpag_horarios">
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
			<input type="text" id="search_horarios" size="45" 
			<?php if(isset($search)) echo 'value="' . $search . '"';?>		
			/>
			<span class="buscar_horarios"><?php echo $html->includeImg('icons/search.png', 'Buscar')?></span>
		</div>
	</div>
</form>

<form method="post" name="formulario_eliminar_horario" id="formulario_eliminar_horario" action="#" class="form">
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
								('<a onclick="load_dataTable(\'horarios\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'desc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('asc.png', 'Ascendente') . '</a>')	:
								('<a onclick="load_dataTable(\'horarios\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) :
							('<a onclick="load_dataTable(\'horarios\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . '</a>')) :
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
			<th class="last">&nbsp;</th>
		</tr> <!-- end cabecera -->
		<?php 
		for ($i = 0; $i < count($data_query); $i++) {
		?>
		<tr class="<?php echo (($i+1)%2==0) ? 'even' : 'odd' ?>"> <!-- cuerpo -->
			<td><input type="checkbox" class="checkbox" id="id_horario" name="id_horario[]" value="<?php echo $data_query[$i]['Horario']['id']?>"/></td>
			<td>
				<?php
				$dia = $multidata['dia'][intval($data_query[$i]['Horario']['dia']) - 1];  
				echo $dia;
				?>
			</td>
			<td>
				<?php
				$hora_inic = substr($data_query[$i]['Horario']['hora_inic'], 0, 5);  
				echo $hora_inic;
				?>
			</td>
			<td>
				<?php
				$hora_fin = substr($data_query[$i]['Horario']['hora_fin'], 0, 5); 
				echo $hora_fin;
				?>
			</td>
			<td>
			<?php 
				echo (strlen($data_query[$i]['Lugar']['nombre'])>40) ? 
				('<span style="cursor: help;" title="' . $data_query[$i]['Lugar']['nombre'] . '">' . rtrim(substr($data_query[$i]['Lugar']['nombre'], 0, 37)) . '...</span>') : 
				($data_query[$i]['Lugar']['nombre']);
			?>
			</td>
			<td class="last">
				<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/ver/' . $data_query[$i]['Horario']['id'] . 'h/' . $id_curso . 'c'?>" 
				style="text-decoration:underline">ver</a> |
				<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/editar/' . $data_query[$i]['Horario']['id'] . 'h/' . $id_curso . 'c'?>" 
				style="text-decoration:underline">editar</a>  |
				<a href="JavaScript:void(0);" style="text-decoration:underline"
				onclick="dataEliminar_horario(<?php echo $data_query[$i]['Horario']['id'] . ', \'' . $dia . '\', \'' . $hora_inic . '\', \'' . $hora_fin . '\''?>);">eliminar</a>
			</td>
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
					echo '<a rel="prev" class="prev_page" onclick="load_dataTable(\'horarios\',' . ($pagina - 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">« Anterior</a>';
				}
				
				if ($i==$pagina) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a onclick="load_dataTable(\'horarios\',' . $i . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">' . $i . '</a>';
				}
				
				if ($i==$pag_ultima && $pagina==$numpag) {
					echo '<span class="disabled next_page">Siguiente »</span>';
				} elseif ($i==$pag_ultima && $pagina!=$numpag) {
					echo '<a rel="next" class="next_page" onclick="load_dataTable(\'horarios\',' . ($pagina + 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
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
           
function dataEliminar_horario(id, dia, hora_inic, hora_fin) {
	$(function() {
		var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
		'¿Está seguro que desea eliminar permanentemente este horario?</p>' + 
		'<p style="margin-left:40px">' + 
			dia + '<br/>' + hora_inic + ' - ' + hora_fin +
		'</p>';
		$( "#dialog-confirm-horario" ).html(msj_confirm);
		$( "#dialog-confirm-horario" ).dialog({
			resizable: false,
			width: 500,
			height: 200,
			buttons: {
				"Sí": function() {
					url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/'?>' + id;
					$.ajax({
						url: url,
						success: function(data) {
							$( "#showMensaje-horarios" ).html(data);
							$( "#showMensaje-horarios" ).fadeIn("slow");
							load_dataTable ('horarios', 1, $( "#regpag_horarios" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_horarios" ).val());
						}
					});
					$( this ).dialog( "close" );
				},
				"No": function() {
					$( this ).dialog( "close" );
				}
			}
		});
	});
}
           
$(function (){

	$("table.table tr td span[title]").qtip({
		position: {
			my: "top center", 
			at: "bottom center"
		},
		style: {
			classes: "ui-tooltip-dark"
		}
	});

	$( "#dialog:ui-dialog" ).dialog( "destroy" );		

	$( ".table :checkbox.toggle" ).each(function(i, toggle) {
		$(toggle).change(function(e) {
			$(toggle).parents("table:first").find(":checkbox:not(.toggle)").each(function(j, checkbox) {
				checkbox.checked = !checkbox.checked;
			});
		});
	});

	$( "#formulario_eliminar_horario" ).submit(function() {

		// no seleccionó ningún perfil
		if ($("input[@id="+id_horario+"]:checked").length == 0) {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'No se ha seleccionado ningún horario.</p>';
			$( "#dialog-confirm-horario" ).html(msj_confirm);
			$( "#dialog-confirm-horario" ).dialog({
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
			'¿Está seguro que desea eliminar permanentemente este (os) horario (s)?</p>';
			$( "#dialog-confirm-horario" ).html(msj_confirm);
			$( "#dialog-confirm-horario" ).dialog({
				resizable: false,
				width: 500,
				height: 160,
				buttons: {
					"Sí": function() {
						/* recorro los checkbox marcados y los agrego al array que enviaré */
						var data_id = new Array();
						$("input:checkbox[id=id_horario]:checked").each(function(){
							data_id.push($(this).val());	   
						});
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar'?>';
						$.post(url, {'id[]': data_id}, function(data){
							$( "#showMensaje-horarios" ).html(data);
							$( "#showMensaje-horarios" ).fadeIn("slow");
							load_dataTable ('horarios', 1, $( "#regpag_horarios" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_horarios" ).val());
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