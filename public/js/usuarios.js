function showTip (campo, id_tip) {
	$(function () {
		var url = url_project + "usuarios/valida_datos";
				
		$.ajax({
			url: url,
			type: "POST",
			dataType: "json",
			data: { 
				campo: campo,
				valor: $( "#" + campo).val(),
			},
			success: function( response ) {
				var validar = response.response;
				$("#" + campo).attr("title", validar.message);
					
				var style = "dark";
						
				if (validar.type=="error") {
					style = "red";
					/* elimino estilos que pueda tener el tooltip */
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-dark");
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-green");
					$("#ui-tooltip-" + id_tip + "-title").html("Error");
				} else if (validar.type=="success") {
					style = "green";
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-dark");
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-red");
					$("#ui-tooltip-" + id_tip + "-title").html("Válido");
				} else {
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-red");
					$("#ui-tooltip-" + id_tip).removeClass("ui-tooltip-green");
					$("#ui-tooltip-" + id_tip + "-title").html("Información");
				}
					$("#ui-tooltip-" + id_tip + " .ui-tooltip-tip").remove();
					$("#ui-tooltip-" + id_tip).addClass("ui-tooltip-" + style);
			}
		});
				
	});
}