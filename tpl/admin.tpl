<div class="card col-md-4">
	<div class="card-header">
		<h3 class="card-header-title">Printed / Invoicing</h3>
	</div>
	<div class="card-body">
		<input type="hidden" name="employee" value="{$id_employee}" />
		<table style="width: 100%">
			<tbody>
				<tr style="font-weight: bold">
					<td>Printed</td>
					<td>Invoicing</td>
				</tr>
				<tr>
					<td>
						<div>
							<span class="ps-switch" id="order_printed">
								<input id="order_printed_0" class="ps-switch" name="order_printed" value="0" {if !$printed} checked {/if} type="radio" />
								<label for="order_printed_0">No</label>
								<input id="order_printed_1" class="ps-switch" name="order_printed" value="1" {if $printed} checked {/if} type="radio">
								<label for="order_printed_1">Yes</label>
								<span class="slide-button"></span>
							</span>
						</div>
					</td>
					<td>
						<div>
							<select id="order_exported" name="order_exported" class="custom-select" aria-label="order_exported">
								<option value="0"{if $exported === 0} selected{/if}>No</option>
								<option value="1"{if $exported === 1} selected{/if}>Invoiced</option>
								<option value="2"{if $exported === 2} selected{/if}>Refuned</option>
							</select>
						</div>
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
