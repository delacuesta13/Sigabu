$(document).ready(function()  {	
	
	// Messages	
	
	/*
	 * Cerrar el mensaje dando click en (cualquier parte de) �ste  
	 */
	
	$('.block .message').click(function() {
		$(this).fadeOut('slow', function() { $(this).remove(); });
	});
	
});