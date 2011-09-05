/**
 * este archivo contiene el código js
 * utilizado en la pestaña inscripciones
 * de la vista ver, del controlador personas.
 */

function load_dataTable_insc (controlador, dni, pag, record, sort, order, search) {
					
	$(function() {
						
		$( "#dynamic-" + controlador ).html( info_preload );
		var url = url_project + "inscripciones/listar_inscripciones_persona/" + dni;
		if(pag.length!=0) url += "/pag=" + pag;
		if(record.length!=0) url += "/record=" + record;
		if(sort.length!=0) url += "/sort=" + sort;
		if(order.length!=0) url += "/order=" + order;
		if(search.length!=0) url += "/q=" + encodeURIComponent(search);
		$.ajax({
			url: url,
			success: function(data) {
				$( "#dynamic-" + controlador ).html(data);
			}
		});
						
	});
		
}

function closeDialog_insc (dialog, id_msj, div){
	
	if (dialog.length!=0) {
		$(function() {
			$("#dialog-" + dialog).dialog("close");  
			return false; 						
		});
	}
	customMensaje_insc(id_msj, div);
		
}

function customMensaje_insc (id_msj, div) {
	
	var mensajes = new Array();
	mensajes[0] = "Vaya! No tienes el permiso necesario para interactuar con la página solicitada.";
	mensajes[1] = "Existe un error al cargar la página solicitada.";	

	var msj_dialog = "<div class=\"message notice\"><p>" + mensajes[id_msj] + "</p></div>"; 
	
	$(function() {
		$( "#showMensaje-" + div ).html(msj_dialog);
		$( "#showMensaje-" + div ).fadeIn("slow");
		$(".flash").click(function() {$(this).fadeOut("slow", function() { $(this).css("display", "none"); });});
	});
	
	return false;

}