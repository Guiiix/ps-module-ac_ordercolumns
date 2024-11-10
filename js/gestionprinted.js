$(document).ready(function() {
	$('input[type="radio"][name="radPrinted"]').change(function() {
		var printed;
		$("#gestionprinted_status_box").html("[...]");
		$.getJSON("{$url}ajax.php?printed="+this.value+"&token={$token}&employee={$id_employee}&order={$id_order}", function(d) {
			if (d['status'] == "success") {
				$("#gestionprinted_status_box").html("[OK]");
			}

			else {
				console.log(d);
				$("#gestionprinted_status_box").html("[ERR]");
			}
		});
	});
});
