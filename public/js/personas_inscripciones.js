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