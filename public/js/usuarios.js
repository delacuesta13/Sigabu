/**
 * 
 * @param campo -> campo que se validará
 * @param valor 
 */
function datosUsuario (campo, valor) {
	var url = url_project + "usuarios/valida_datos";
	var rs_type = '';
	var rs_message = '';
	$(function () {
		$.ajax({
			url: url,
			async: false,
			type: "POST",
			dataType: "json",
			data: { 
				campo: campo,
				valor: valor,
			},
			success: function( response ) {
				rs_type = response.response.type;
				rs_message = response.response.message;
			}
		});
	});
	var datos = {'type': rs_type, 'message': rs_message};
	return datos;
}