$(document).ready(function() {
	$('input[type="radio"][name="radExported"]').change(function() {
		var exported;
		$("#gestionexported_status_box").html("[...]");
		$.getJSON("{$url}ajax.php?exported="+this.value+"&token={$token}&employee={$id_employee}&order={$id_order}", function(d) {
			if (d['status'] == "success") {
				$("#gestionexported_status_box").html("[OK]");
			}

			else {
				console.log(d);
				$("#gestionexported_status_box").html("[ERR]");
			}
		});
	});
});
