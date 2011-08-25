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

<div class="group navform wat-cf" id="botonera" style="margin-bottom:2px">
	<a href="<?php echo BASE_PATH . '/' . strtolower($this->_controller) . '/' . 'nuevo'?>">
		<button class="button" id="btn_nuevo">
				<?php echo $html->includeImg('icons/add.png', 'Nuevo')?> Nuevo
		</button>
	</a>
</div>

<div id="showMensaje" class="flash" style="display:none;margin-top:15px"></div>

<div id="dynamic" style="padding-top:15px;"> <!-- div donde cargo el ajax -->
	
</div> <!-- end dynamic -->
