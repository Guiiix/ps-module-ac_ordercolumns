$(document).ready(function() {
	$('select[name="order_exported"]').change(function() {
		$.getJSON("{$url}ajax.php?exported="+this.value+"&token={$token}&employee={$id_employee}&order={$id_order}", function(d) {
			if (d['status'] !== "success") {
				console.log("Erreur lors de la mise à jour de l'état de facturation de la commande");
				console.log(d);
			}
		});
	});

    $('input[type="radio"][name="order_printed"]').change(function() {
		$.getJSON("{$url}ajax.php?printed="+this.value+"&token={$token}&employee={$id_employee}&order={$id_order}", function(d) {
			if (d['status'] !== "success") {
				console.log("Erreur lors de la mise à jour de l'état d'impression de la commande");
				console.log(d);
			}
		});
	});
});
