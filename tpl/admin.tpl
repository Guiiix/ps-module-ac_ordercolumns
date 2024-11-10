<div class="card col-md-4">
	<div class="card-header">
		<h3 class="card-header-title">Impression et facturation</h3>
	</div>
	<div class="card-body">
		<input type="hidden" name="employee" value="{$id_employee}" />
		<table style="width: 100%">
			<tbody>
				<tr style="font-weight: bold">
					<td>Imprimé</td>
					<td>Facturé</td>
				</tr>
				<tr>
					<td>
						<div>
							<img src="/img/admin/enabled.gif" alt="">
							<input type="radio" name="radPrinted" id="radPrintedY" {if $printed} checked {/if} value="1" />

							<img src="/img/admin/disabled.gif">
							<input type="radio" name="radPrinted" id="radPrintedN" {if !$printed} checked {/if} value="0" />

							<div style="display:inline-block" id="gestionprinted_status_box"></div>
						</div>
					</td>
					<td>
						<div>
							<img src="/img/admin/enabled.gif" alt="">
							<input type="radio" name="radExported" id="radExportedY" {if $exported} checked {/if} value="1" />

							<img src="/img/admin/disabled.gif">
							<input type="radio" name="radExported" id="radExportedN" {if !$exported} checked {/if} value="0" />

							<div style="display:inline-block" id="gestionexported_status_box"></div>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
