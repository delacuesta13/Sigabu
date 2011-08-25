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

	$( "#reg_pag" ).change(function() {
		load_dataTable (1, $( "#reg_pag" ).val(), '<?php echo $sort?>', 'asc', $( "#search" ).val());
		
	});

	$( ".buscar" ).bind("click", function() {
		load_dataTable (1, $( "#reg_pag" ).val(), '<?php echo $sort?>', 'asc', $( "#search" ).val());
	});	

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).remove(); });
	});

});

//]]>
</script>

<form action="#" method="get" class="form" style="margin-bottom:15px">
<div class="columns wat-cf">
	<div class="column left">
		<label class="label">Mostrar</label>
		<select id="reg_pag">
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
		<input type="text" id="search" size="45" 
		<?php if(isset($search)) echo 'value="' . $search . '"';?>		
		/>
		<span class="buscar"><?php echo $html->includeImg('icons/search.png', 'Buscar')?></span>
	</div>
</div>
</form>

<form method="post" name="formulario" id="formulario" action="#" class="form">
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
								('<a onclick="load_dataTable(1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'desc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('asc.png', 'Ascendente') . '</a>')	: 
								('<a onclick="load_dataTable(1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) : 
							('<a onclick="load_dataTable(1, ' . $record . ', \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\', \'\');" style="cursor:pointer;">' . $attr['text'] . '</a>')) : 
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
			<td><input type="checkbox" class="checkbox" id="id_act" name="id[]" value="<?php echo $data_query[$i]['Act']['id']?>"/></td>
			<td><?php echo $data_query[$i]['Act']['nombre']?></td>
			<td><?php echo $data_query[$i]['Area']['nombre']?></td>
			<td class="last">
				<a onclick="loadActividad(<?php echo $data_query[$i]['Act']['id']?>);" style="text-decoration: underline; cursor: pointer;">ver</a> |
				<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'editar' . '/' . $data_query[$i]['Act']['id']?>" style="text-decoration: underline;">editar</a> |
				<a onclick="dataEliminar(<?php echo $data_query[$i]['Act']['id']?>, '<?php echo $data_query[$i]['Act']['nombre']?>', '<?php echo $data_query[$i]['Area']['nombre']?>');" style="text-decoration: underline;cursor: pointer;">eliminar</a>
			</td>
		</tr> <!-- end cuerpo -->
		<?php 
		} /* for */ 
		## no hay registros para mostrar
		if(count($data_query)==0){
		?>
		<tr> <!-- cuerpo -->
			<td colspan="10" style="text-align: center;">Vaya! No se encontraron registros.</td>
		</tr> <!-- end cuerpo -->
		<?php		
		}
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
			$btn_atras = true;
			$btn_delante = true;
			
			## imprimo paginación
			for ($i = $pag_inicio; $i <= $pag_ultima; $i++) {
				
				if ($i==$pag_inicio && $pagina==1) {
					echo '<span class="disabled prev_page">« Anterior</span>';
				} elseif ($i==$pag_inicio && $pagina!=1) {
					echo '<a rel="prev" class="prev_page" onclick="load_dataTable(' . ($pagina - 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');" 
					style="cursor:pointer">« Anterior</a>';
				}
				
				if ($i==$pagina) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a onclick="load_dataTable(' . $i . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
					style="cursor:pointer">' . $i . '</a>';
				}
				
				if ($i==$pag_ultima && $pagina==$numpag) {
					echo '<span class="disabled next_page">Siguiente »</span>';
				} elseif ($i==$pag_ultima && $pagina!=$numpag) {
					echo '<a rel="next" class="next_page" onclick="load_dataTable(' . ($pagina + 1) . ', ' . $record . ', \'' . $sort . '\', \'' . $order . '\', \'' . $q . '\');"
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

<div id="dialog-confirm" title="Eliminar actividad" style="display: none;"></div>

<script type="text/JavaScript">
//<![CDATA[
           
   function loadActividad (id) {

	   $(function() {
			url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/ver/'?>' + id;
			var info_preload = '<div id="info_preload" class="dataTablas_preload">Cargando...</div>';
			$( "#dynamic" ).html( info_preload );
			$.ajax({
				url: url,
				success: function(data) {
					$( "#dynamic" ).html(data);
				}
			});
	   });
		
   }
           
   function dataEliminar(id, nombre, area) {
		$(function() {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'¿Está seguro que desea eliminar permanentemente esta actividad?</p>' + 
			'<p style="margin-left:40px">' + 
				nombre + '<br/>' + area +
			'</p>';
			$( "#dialog-confirm" ).html(msj_confirm);
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				width: 500,
				height: 200,
				buttons: {
					"Sí": function() {
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar/'?>' + id;
						$.ajax({
							url: url,
							success: function(data) {
								$( "#showMensaje" ).html(data);
								$( "#showMensaje" ).fadeIn("slow");
								load_dataTable (1, $( "#reg_pag" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search" ).val());
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

   $(function() {

		$( "#dialog:ui-dialog" ).dialog( "destroy" );		

		$( ".table :checkbox.toggle" ).each(function(i, toggle) {
			$(toggle).change(function(e) {
				$(toggle).parents("table:first").find(":checkbox:not(.toggle)").each(function(j, checkbox) {
					checkbox.checked = !checkbox.checked;
				});
			});
		});

		$( "#formulario" ).submit(function() {

			// no seleccionó ninguna persona
			if ($("input[@id="+id_act+"]:checked").length == 0) {
				var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
				'No se ha seleccionado ninguna actividad.</p>';
				$( "#dialog-confirm" ).html(msj_confirm);
				$( "#dialog-confirm" ).dialog({
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
				'¿Está seguro que desea eliminar permanentemente esta (s) actividad (s)?</p>';
				$( "#dialog-confirm" ).html(msj_confirm);
				$( "#dialog-confirm" ).dialog({
					resizable: false,
					width: 500,
					height: 160,
					buttons: {
						"Sí": function() {
							/* recorro los checkbox marcados y los agrego al array que enviaré */
							var data_id = new Array();
							$("input:checkbox[id=id_act]:checked").each(function(){
								data_id.push($(this).val());	   
							});
							url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar'?>';
							$.post(url, {'id[]': data_id}, function(data){
								$( "#showMensaje" ).html(data);
								$( "#showMensaje" ).fadeIn("slow");
								load_dataTable (1, $( "#reg_pag" ).val(), '<?php echo $sort?>', '<?php echo $order?>', $( "#search" ).val());
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