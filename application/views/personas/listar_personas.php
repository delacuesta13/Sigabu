<?php include_once ROOT . DS . 'library/fechas.funciones.php';?>
<form method="post" name="formulario" id="formulario" action="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'eliminar';?>" class="form">
	<table class="table">
		<tr> <!-- cabecera -->
			<th class="first"><input type="checkbox" class="checkbox toggle" /></th>
			<?php 			
			foreach($campos_tabla as $campo => $def){
			?>
			<th class="<?php echo $campo?>">
				<?php 
				echo  (($def['sort']) ? ((strtolower($sort)==strtolower($campo)) ? ((strtolower($order)=='asc') ? '<a onclick="load_dataTable(1, \''. $campo .'\', \'desc\' );" style="cursor:pointer">'. $def['text'] .  $html->includeImg('asc.png', 'Ascendente') .'</>' : '<a onclick="load_dataTable(1, \''. $campo .'\', \'asc\' );" style="cursor:pointer">'. $def['text'] . $html->includeImg('desc.png', 'Descendente') .'</>') : 
				('<a onclick="load_dataTable(1, \''. $campo .'\', \'asc\' );" style="cursor:pointer">'. $def['text'] .'</>')) : 
				($def['text']));
				?>
			</th>			
			<?php 
			}
			unset($campo, $def);
			?>
			<th class="last">&nbsp;</th>			
		</tr> <!-- end cabecera -->
		<?php 
		for($i = 0; $i < count($data_query); $i++){
		?>
		<tr class="<?php echo (($i+1)%2==0) ? 'even' : 'odd' ?>"> <!-- cuerpo -->
			<td><input type="checkbox" class="checkbox" name="dni[]" value="<?php echo $data_query[$i]['Persona']['dni']?>"/></td>		
			<td><?php echo $data_query[$i]['Persona']['tipo_dni']?></td>		
			<td><?php echo $data_query[$i]['Persona']['dni']?></td>		
			<td><?php echo $data_query[$i]['Persona']['nombres']?></td>		
			<td><?php echo $data_query[$i]['Persona']['apellidos']?></td>		
			<td>
			<?php
			if (strlen($data_query[$i]['Persona']['fecha_nac'])!=0) { 
				$edad = calcular_dif_fechas($data_query[$i]['Persona']['fecha_nac'], date('Y-m-d'));
				echo $data_query[$i]['Persona']['fecha_nac'] . ' (' . $edad['years'] . ')';
			}
			?>
			</td>		
			<td><?php echo ($data_query[$i]['Persona']['genero']=='H') ? 'Hombre' : 'Mujer'?></td>		
			<td><?php echo ($data_query[$i]['Persona']['monitor']=='1') ? $html->includeImg('icons/tick.png', 'Activo') : $html->includeImg('icons/cross.png', 'No')?></td>		
			<td><?php echo ($data_query[$i]['Persona']['estado']=='1') ? $html->includeImg('icons/tick.png', 'Activo') : $html->includeImg('icons/cross.png', 'No')?></td>
			<td class="last">
				<?php echo $html->link('ver', strtolower($this->_controller) . '/ver/' . $data_query[$i]['Persona']['dni'])?> |
				<?php echo $html->link('editar', strtolower($this->_controller) . '/editar/' . $data_query[$i]['Persona']['dni'])?> |
				<?php echo $html->link('eliminar', strtolower($this->_controller) . '/eliminar/' . $data_query[$i]['Persona']['dni'])?>
			</td>		
		</tr> <!-- end cuerpo -->		
		<?php
		} 
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
		
		if($numpag > 1){
		?>
		<div class="pagination">
			<?php			
			if ($pagina==1) {
				?><span class="disabled prev_page">« Anterior</span><?php 			
			} else {
				echo '<a  rel="prev" class="prev_page" onclick="load_dataTable('.($pagina - 1).', \''.$sort.'\', \''.$order.'\')" style="cursor:pointer">« Anterior</a>';
			}
			 
			$inic_pag = (($pagina >= $numpag_antes) ? ($pagina - $numpag_antes) : 0); ## página inicial de paginación	
			if($inic_pag==0) $inic_pag++;
			
			## ubico botones hasta el número de la página actual - 1
			for($i = $inic_pag; $i < $pagina;$i++){
				echo '<a onclick="load_dataTable('.$i.', \''.$sort.'\', \''.$order.'\')" style="cursor:pointer">'.$i.'</a>';
			}
			
			## imprimo botón página actual
			echo '<span class="current">'.$pagina.'</span>';
			
			$fin_pag = ((($pagina + $numpag_desp) > $numpag) ? $numpag : ($pagina + $numpag_desp));
			
			## ubico botones desde el número de la página + 1, hasta el número de la página + 3
			for($i = $pagina + 1; $i <= $fin_pag; $i++){
				echo '<a onclick="load_dataTable('.$i.', \''.$sort.'\', \''.$order.'\')" style="cursor:pointer">'.$i.'</a>';
			}
			
			if ($pagina==$numpag) {
				?><span class="disabled next_page">Siguiente »</span><?php
			} else {
				echo '<a  rel="next" class="next_page" onclick="load_dataTable('.($pagina + 1).', \''.$sort.'\', \''.$order.'\')" style="cursor:pointer">Siguiente »</a>';
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
	$(function() {
		$( ".table :checkbox.toggle" ).each(function(i, toggle) {
			$(toggle).change(function(e) {
				$(toggle).parents("table:first").find(":checkbox:not(.toggle)").each(function(j, checkbox) {
					checkbox.checked = !checkbox.checked;
				});
			});
		});
	
	});           
//]]>
</script>