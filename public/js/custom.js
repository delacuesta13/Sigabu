/*
 * Copyright (c) 2011 Jhon Adrián Cerón <jadrian.ceron@gmail.com>
 *
 * This file is part of the Sigabu project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$(document).ready(function()  {	
	
	// Messages	
	
	/*
	 * Cerrar el mensaje dando click en (cualquier parte de) éste  
	 */
	
	$('.block .message').click(function() {
		$(this).fadeOut('slow', function() { $(this).remove(); });
	});
	
});