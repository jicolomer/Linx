<div class="modal fade" id="adjuntar-doc-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="adjuntar-doc-modal-title-label">Adjuntar documentos al contrato</h4>
			</div>
			<form id="adjuntar-doc-modal-form" class="bootstrap-modal-form form-horizontal">
				<div class="modal-body">
					<table id="adjuntar-doc-modal-table">
						<thead>
							<tr>
								<th></th>
								<th class="text-right" width="50px">ID</th>
								<th>Documento</th>
								<th>Ámbito</th>
								<th>Fecha Doc.</th>
								<th>Caducidad</th>
								<th>Validado</th>
								<th>Notas</th>
								<th>Palabras clave</th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
				<div class="modal-footer">
					<button type="button" id="adjuntar-doc-modal-add-doc-button" class="btn btn-success pull-left bootstrap-modal-form-open" data-toggle="modal" data-target="#documentos-modal-dialog" data-new="true"><i class="fa fa-plus"></i> &nbsp; Añadir documento</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" id="adjuntar-doc-modal-go-button" class="btn btn-primary"><i class="fa fa-exchange"></i> &nbsp; Adjuntar documentos</button>
				</div>
				<input type="hidden" id="adjuntar-doc-modal-filter-field" name="filter" value="" />
			</form>
		</div>
	</div>
</div>
