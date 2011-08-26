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

<form method="post" name="formulario" id="formulario" action="#" class="form">
	<table class="table">
		<tr> <!-- cabecera -->
			<th class="first"><input type="checkbox" class="checkbox toggle" /></th>
			<?php 
			foreach ($campos_tabla as $field => $def) {
				if ($def['showTable']) {
				?>
				<th class="<?php echo $field?>">
				<?php 
				echo (($def['sort']) ? ((strtolower($sort)==strtolower($field)) ? ((strtolower($order)=='asc') ? 
						('<a onclick="load_dataTable(1, \'' . $field . '\', \'desc\');" style="cursor: pointer;">' . $def['text'] . $html->includeImg('asc.png', 'Ascendente') .'</a>') : 
						('<a onclick="load_dataTable(1, \'' . $field . '\', \'asc\');" style="cursor: pointer;">' . $def['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) : 
					('<a onclick="load_dataTable(1, \'' . $field . '\', \'desc\');" style="cursor: pointer;">' . $def['text'] . '</a>')) : 
				($def['text']));
				?>
				</th>
				<?php 	
				}
			}
			unset($field, $def)
			?>
			<th class="last">&nbsp;</th>
		</tr><!-- end cabecera -->
		<?php 
		for ($i = 0; $i < count($data_query); $i++) {
		?>	
		<tr class="<?php echo (($i+1)%2==0) ? 'even' : 'odd' ?>"> <!-- cuerpo -->
			<td><input type="checkbox" class="checkbox" id="id_lugar" name="id[]" value="<?php echo $data_query[$i]['Lugar']['id']?>"/></td>
			<td>
				<?php 
				echo ((strlen($data_query[$i]['Lugar']['nombre'])>50) ? 
					('<span title="' . $data_query[$i]['Lugar']['nombre'] . '">' . substr($data_query[$i]['Lugar']['nombre'], 0, 47) . '...' . '</span>') :
					($data_query[$i]['Lugar']['nombre']));
				?>
			</td>
			<td>
				<?php 
				echo ((strlen($data_query[$i]['Lugar']['direccion'])>30) ? 
					('<span title="' . $data_query[$i]['Lugar']['direccion'] . '">' . substr($data_query[$i]['Lugar']['direccion'], 0, 27) . '...' . '</span>') :
					($data_query[$i]['Lugar']['direccion']));
				?>
			</td>
			<td class="last">
				<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'ver' . '/' . $data_query[$i]['Lugar']['id']?>" 
				style="text-decoration: underline;">ver</a> |
				<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'editar' . '/' . $data_query[$i]['Lugar']['id']?>" 
				style="text-decoration: underline;">editar</a> |
				<a onclick="dataEliminar(<?php echo $data_query[$i]['Lugar']['id']?>, '<?php echo $data_query[$i]['Lugar']['nombre']?>');" 
				style="text-decoration: underline;cursor: pointer;">eliminar</a>
			</td>
		</tr> <!-- end cuerpo -->
		<?php	
		} /* for */
		## no se encontraron registros
		if (count($data_query)==0) {
		?>
		<tr> <!-- body -->
			<td colspan="5" style="text-align:center">
				Vaya! No se encontraron registros.
			</td>
		</tr> <!-- end body -->
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
			
			## imprimo paginación
			for ($i = $pag_inicio; $i <= $pag_ultima; $i++) {
				
				if ($i==$pag_inicio && $pagina==1) {
					echo '<span class="disabled prev_page">« Anterior</span>';
				} elseif ($i==$pag_inicio && $pagina!=1) {
					echo '<a rel="prev" class="prev_page" onclick="load_dataTable(' . ($pagina - 1) . ', \'' . $sort . '\', \'' . $order . '\');"
						style="cursor:pointer">« Anterior</a>';
				}
				
				if ($i==$pagina) {
					echo '<span class="current">' . $i . '</span>';
				} else {
					echo '<a onclick="load_dataTable(' . $i . ', \'' . $sort . '\', \'' . $order . '\');"
						style="cursor:pointer">' . $i . '</a>';
				}
				
				if ($i==$pag_ultima && $pagina==$numpag) {
					echo '<span class="disabled next_page">Siguiente »</span>';
				} elseif ($i==$pag_ultima && $pagina!=$numpag) {
					echo '<a rel="next" class="next_page" onclick="load_dataTable(' . ($pagina + 1) . ', \'' . $sort . '\', \'' . $order . '\');"
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

<div id="dialog-confirm" title="Eliminar lugar" style="display: none;"></div>

<script type="text/JavaScript">
//<![CDATA[
           
	function dataEliminar (id, lugar) {
		$(function() {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'¿Está seguro que desea eliminar permanentemente este lugar?</p>' + 
			'<p style="margin-left:40px">' + 
				lugar +
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
								load_dataTable (1, '<?php echo $sort?>', '<?php echo $order?>');
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

	$('.flash').click(function() {
		$(this).fadeOut('slow', function() { $(this).css('display','none'); });
	});

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
		if ($("input[@id="+id_lugar+"]:checked").length == 0) {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'No se ha seleccionado ningún lugar.</p>';
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
			'¿Está seguro que desea eliminar permanentemente este (os) lugar (es)?</p>';
			$( "#dialog-confirm" ).html(msj_confirm);
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				width: 500,
				height: 160,
				buttons: {
					"Sí": function() {
						/* recorro los checkbox marcados y los agrego al array que enviaré */
						var data_id = new Array();
						$("input:checkbox[id=id_lugar]:checked").each(function(){
							data_id.push($(this).val());	   
						});
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar'?>';
						$.post(url, {'id[]': data_id}, function(data){
							$( "#showMensaje" ).html(data);
							$( "#showMensaje" ).fadeIn("slow");
							load_dataTable (1, '<?php echo $sort?>', '<?php echo $order?>');
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