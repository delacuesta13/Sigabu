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

<script type="text/JavaScript">
//<![CDATA[
$(function() {

	$( "#regpag_inscripciones" ).change(function() {
		load_dataTable ('inscripciones', 1, $( "#regpag_inscripciones" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_inscripciones" ).val());
		
	});

	$( ".buscar_inscripciones" ).bind("click", function() {
		load_dataTable ('inscripciones', 1, $( "#regpag_inscripciones" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_inscripciones" ).val());
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
			<select id="regpag_inscripciones">
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
			<input type="text" id="search_inscripciones" size="45" 
			<?php if(isset($search)) echo 'value="' . $search . '"';?>		
			/>
			<span class="buscar_inscripciones"><?php echo $html->includeImg('icons/search.png', 'Buscar')?></span>
		</div>
	</div>
</form>

<form method="post" name="formulario_eliminar_inscripcion" id="formulario_eliminar_inscripcion" action="#" class="form">
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
								('<a onclick="load_dataTable(\'inscripciones\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'desc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('asc.png', 'Ascendente') . '</a>')	:
								('<a onclick="load_dataTable(\'inscripciones\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) :
							('<a onclick="load_dataTable(\'inscripciones\', 1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . '</a>')) :
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
			<td><input type="checkbox" class="checkbox" id="id_inscripcion" name="id_inscripcion[]" value="<?php echo $data_query[$i]['Inscripcion']['id']?>"/></td>
			<td><?php echo $data_query[$i]['Persona']['dni']?></td>
			<td><?php echo $data_query[$i]['Persona']['nombres']?></td>
			<td><?php echo $data_query[$i]['Persona']['apellidos']?></td>
			<td><?php echo $data_query[$i]['Multientidad']['nombre']?></td>
			<td><?php echo substr($data_query[$i]['Inscripcion']['fecha_inscripcion'], 0, 16)?></td>
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
					echo '<a rel="prev" class="prev_page" onclick="load_dataTable(\'inscripciones\',' . ($pagina - 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">« Anterior</a>';
				}
				
				if ($i==$pagina) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a onclick="load_dataTable(\'inscripciones\',' . $i . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">' . $i . '</a>';
				}
				
				if ($i==$pag_ultima && $pagina==$numpag) {
					echo '<span class="disabled next_page">Siguiente »</span>';
				} elseif ($i==$pag_ultima && $pagina!=$numpag) {
					echo '<a rel="next" class="next_page" onclick="load_dataTable(\'inscripciones\',' . ($pagina + 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
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

	$( "#formulario_eliminar_inscripcion" ).submit(function() {

		// no seleccionó ninguna inscripción
		if ($("input[@id="+id_inscripcion+"]:checked").length == 0) {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'No se ha seleccionado ninguna inscripción.</p>';
			$( "#dialog-confirm-inscripcion" ).html(msj_confirm);
			$( "#dialog-confirm-inscripcion" ).dialog({
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
			'¿Está seguro que desea eliminar permanentemente esta (s) inscripción (es)?</p>';
			$( "#dialog-confirm-inscripcion" ).html(msj_confirm);
			$( "#dialog-confirm-inscripcion" ).dialog({
				resizable: false,
				width: 500,
				height: 160,
				buttons: {
					"Sí": function() {
						/* recorro los checkbox marcados y los agrego al array que enviaré */
						var data_id = new Array();
						$("input:checkbox[id=id_inscripcion]:checked").each(function(){
							data_id.push($(this).val());	   
						});
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/' . $id_curso?>';
						$.post(url, {'id[]': data_id}, function(data){
							$( "#showMensaje-inscripciones" ).html(data);
							$( "#showMensaje-inscripciones" ).fadeIn("slow");
							load_dataTable ('inscripciones', 1, $( "#regpag_inscripciones" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search_inscripciones" ).val());
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