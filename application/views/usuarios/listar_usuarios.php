<?php 
/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
$lista_meses = array('Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic');
?>

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
								('<a onclick="load_dataTable(1, \'' . strtolower($def['alias'] . '.' . $field) . '\', \'desc\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('asc.png', 'Ascendente') . '</a>')	: 
								('<a onclick="load_dataTable(1, \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\');" style="cursor:pointer;">' . $attr['text'] . $html->includeImg('desc.png', 'Descendente') . '</a>')) : 
							('<a onclick="load_dataTable(1, \'' . strtolower($def['alias'] . '.' . $field) . '\', \'asc\');" style="cursor:pointer;">' . $attr['text'] . '</a>')) : 
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
			<th class="last" style="width: 15px !important">&nbsp;</th>
		</tr><!-- end cabecera -->
		<?php
		## imprimir registros
		for ($i = 0; $i < count ($data_query); $i++) {
		?>
		<tr class="<?php echo (($i+1)%2==0) ? 'even' : 'odd' ?>"> <!-- cuerpo -->
			<td><input type="checkbox" class="checkbox" id="dni_persona" name="dni[]" value="<?php echo $data_query[$i]['Persona']['dni']?>"/></td>
			<td><?php echo $data_query[$i]['Persona']['dni']?></td>
			<td>
				<?php 
				$persona = $data_query[$i]['Persona']['nombres'] . ' ' . $data_query[$i]['Persona']['apellidos'];
				echo (strlen($persona) > 25) ? ('<span title="' . $persona . '">' . rtrim(substr($persona, 0, 22)) . '...' . '</span>') : $persona;
				?>
			</td>
			<td>
				<?php 
				$rol = $data_query[$i]['Rol']['nombre'];
				echo ((strlen($rol)>20) ? 
						('<span title="' . $rol . '">' . (rtrim(substr($rol, 0, 17)) . '...') . '</span>') :
						($rol));
				?>
			</td>
			<td><?php echo $data_query[$i]['Usuario']['username']?></td>
			<td><?php echo $data_query[$i]['Usuario']['email']?></td>
			<td>
				<?php 
				echo (($data_query[$i]['Usuario']['estado']==1) ? 
					($html->includeImg('icons/tick.png', 'Activo')) : 
					($html->includeImg('icons/cross.png', 'Inactivo')));
				?>
			</td>
			<td>
				<?php
				if (strlen($data_query[$i]['Usuario']['fecha_activacion'])!=0) {
					$fecha = explode('-', substr($data_query[$i]['Usuario']['fecha_activacion'], 0, 10));
					echo '<span title="' . $fecha[2] . ' ' . $lista_meses[intval($fecha[1]) - 1] . ' ' . $fecha[0] . '">' .
						  substr($data_query[$i]['Usuario']['fecha_activacion'], 0, 16) .
						  '</span>';
				}    
				?>
			</td>
			<td>
				<?php
				if (strlen($data_query[$i]['Usuario']['ultima_visita'])!=0) {
					$fecha = explode('-', substr($data_query[$i]['Usuario']['ultima_visita'], 0, 10));
					echo '<span title="' . $fecha[2] . ' ' . $lista_meses[intval($fecha[1]) - 1] . ' ' . $fecha[0] . '">' .
						  substr($data_query[$i]['Usuario']['ultima_visita'], 0, 16) .
						  '</span>';
				}    
				?>
			</td>
			<td class="last">
				<?php echo $html->link('editar', 'usuarios/editar/' . $data_query[$i]['Persona']['dni']);?>
			</td>
		</tr> <!-- end cuerpo -->
		<?php	
		}
		## no se encontraronr registros
		if (count($data_query)==0) {
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

<div id="dialog-confirm" title="Eliminar usuario" style="display: none;"></div>

<script type="text/JavaScript">
//<![CDATA[
           
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

		// no seleccionó ningún usuario
		if ($("input[@id="+dni_persona+"]:checked").length == 0) {
			var msj_confirm = '<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 10px 0;"></span>' + 
			'No se ha seleccionado ningún usuario.</p>';
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
			'¿Está seguro que desea eliminar permanentemente este (os) usuario (s)?</p>';
			$( "#dialog-confirm" ).html(msj_confirm);
			$( "#dialog-confirm" ).dialog({
				resizable: false,
				width: 520,
				height: 160,
				buttons: {
					"Sí": function() {
						/* recorro los checkbox marcados y los agrego al array que enviaré */
						var data_id = new Array();
						$("input:checkbox[id=dni_persona]:checked").each(function(){
							data_id.push($(this).val());	   
						});
						url = '<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/eliminar'?>';
						$.post(url, {'persona[]': data_id}, function(data){
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

//]]>
</script>