<link href="{{ asset('/plugins/selectize/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
<div class="modal fade" id="documentos-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="documentos-modal-title-label">Añadir Documento</h4>
			</div>
			<form id="documentos-modal-form" class="bootstrap-modal-form form-horizontal">
				<input type="hidden" name="_action" id="_action" value="new">
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div id="documentos-modal-errors-box" class="alert alert-danger" role="alert">
								<h4><i class="icon fa fa-ban"></i> ¡Vaya! Hay algunos problemas con su entrada.</h4>
								<ul></ul>
							</div>
						</div>
					</div>
					{{ Form::fhText('id', null, null, false, 4, null, ['readonly' => true]) }}
					{{ Form::fhSelect('tipo_documento_id', $tipos_documentos, null, 'Tipo de documento', 'Seleccione el tipo de documento...', true, [], 10) }}
					<input type="hidden" id="tipo_documento_ambito" value="" />
					{{ Form::fhText('nombre_documento', null, null, true) }}
					{{ Form::fhDate('fecha_documento', null, true) }}
					<div id="caducidad_group" class="form-group{{ $errors->has('fecha_caducidad') ? ' has-error' : '' }}">
						{{ Form::label('fecha_caducidad', 'Fecha de Caducidad', ['class' => 'control-label col-sm-2']) }}
						<div class="col-md-4">
							<input type='text' id="fecha_caducidad" name="fecha_caducidad" class="form-control" readonly>
						</div>
						<div class="col-md-2">
							<input type='hidden' id="tipo_caducidad" name="tipo_caducidad">
							<span id="tipo_caducidad_text" class="badge bg-green" style="margin-top:8px"></span>
						</div>
					</div>
					{{ Form::fhText('horas_formacion', 'Horas de Formación', 'Número de horas', false, 4) }}
					{{ Form::fhTextarea('notas') }}
					{{ Form::fhText('version', 'Versión', null, true, 4, \Carbon\Carbon::now()->format('Ymd') . '01', ['readonly' => true]) }}
					{{ Form::fhText('tags', 'Palabras clave', 'Lista de palabras clave separadas por comas', false) }}
					<input type="hidden" id="tipo_documento_tags" value="" />
					{{ Form::fhFile('file', 'Fichero', 'Seleccione', true) }}
					<div id="download-file-group" class="form-group">
						{{ Form::label('filename', 'Fichero', ['class' => 'control-label col-sm-2']) }}
						<div class="col-sm-4">
							{{ Form::text('filename', null, ['class' => 'form-control', 'readonly' => true]) }}
						</div>
						<div class="col-sm-2">
							<a target="_blank" type="button" id="documentos-modal-download-button" class='btn btn-success'><i class="fa fa-download"></i> Descargar</a>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" id="documentos-modal-new-version-button" class="btn btn-warning pull-left"><i class="fa fa-plus"></i> Nueva versión</button>
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
					<button type="button" id="documentos-modal-go-button" class="btn btn-primary bootstrap-modal-form-open">Guardar</button>
				</div>
			</form>
		</div>
	</div>
</div>
<div class="modal fade" id="versiones-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Versiones anteriores del Documento</h4>
				<h5 class="modal-title" id="versiones-modal-document-name"></h5>
			</div>
			<div class="modal-body">
				<table id="versiones-modal-table">
					<thead>
						<tr>
							<th>Versión</th>
							<th>Fecha Doc.</th>
							<th>Fecha Caducidad</th>
							<th>Notas</th>
							<th>Fecha Archivado</th>
							<th></th>
						</tr>
					</thead>
					<tbody></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>
