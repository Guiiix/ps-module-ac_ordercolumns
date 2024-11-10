<div class="panel" style="width: 400px;margin-top:10px;">
	<div class="panel-heading"><img src="{$url}logo.gif" />Champ imprimé</div>
	<input type="hidden" name="employee" value="{$id_employee}" />
	<label for="exported">Imprimé :</label>
	
	<img src="/img/admin/enabled.gif" alt="">
	<input type="radio" name="radPrinted" id="radPrintedY" {if $printed} checked {/if} value="1" />

	<img src="/img/admin/disabled.gif">
	<input type="radio" name="radPrinted" id="radPrintedN" {if !$printed} checked {/if} value="0" />

	<div style="display:inline-block" id="gestionprinted_status_box"></div>
</div>

<div class="panel" style="width: 400px;margin-top:10px;">
	<div class="panel-heading"><img src="{$url}logo.gif" />Champ Facturé</div>
	<input type="hidden" name="employee" value="{$id_employee}" />
	<label for="exported">Facturé :</label>
	
	<img src="/img/admin/enabled.gif" alt="">
	<input type="radio" name="radExported" id="radExportedY" {if $exported} checked {/if} value="1" />

	<img src="/img/admin/disabled.gif">
	<input type="radio" name="radExported" id="radExportedN" {if !$exported} checked {/if} value="0" />

	<div style="display:inline-block" id="gestionexported_status_box"></div>
</div>