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
