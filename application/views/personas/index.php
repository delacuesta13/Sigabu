<?php 

/*
 * Copyright (c) 2011 Jhon Adri�n Cer�n <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if(isset($showMensaje)){
	?>
	<div class="flash">
		<div class="message notice">
			<p><?php echo $showMensaje?></p>
		</div>
	</div>
	<?php 
}
?>

<form action="#" method="get" class="form">
<div class="columns wat-cf">
	<div class="column left">
		<label class="label">Mostrar</label>
		<select id="reg_pag">
			<option value="10" selected="selected">10</option>
			<option value="20">20</option>
			<option value="50">50</option>
			<option value="100">100</option>
		</select>
		registros por p�gina
	</div>
	<div class="column right">
		<label class="label">Buscar</label>
		<input type="text" id="search" size="45"/>
	</div>
</div>
</form>
<div id="dynamic" style="padding-top:15px;"> <!-- div donde cargo el ajax -->
	
</div> <!-- end dynamic -->
