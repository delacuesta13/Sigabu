
						<div> <!-- end inner -->
					</div> <!-- end content  -->
				<div> <!-- end block -->
			
			</div> <!-- end main -->
			<div id="sidebar">
			<?php
			/*
			 * Agregar HTML al div 'sidebar'
			 * 
			 * Declarar variable 'make_sidebar' y en ésta
			 * agregar el contenido a mostrar.
			 */
			if(isset($make_sidebar) && strlen($make_sidebar)!=0){
				echo $make_sidebar;
			}
			
			?>
			</div> <!-- end sidebar -->

			<?php 
			$text_footer = 'Copyright &copy; 2011 Jhon Adrián Cerón Guzmán.';
			?>
			
			<script type="text/javascript">
			<!--
			$('#main').append('<div id="footer"><div class="block"><p><?php echo $text_footer?><\/p><\/div><\/div>');
			//-->
			</script>
		
		</div> <!-- end wrapper -->
	</div> <!-- end container -->
</body>
</html>