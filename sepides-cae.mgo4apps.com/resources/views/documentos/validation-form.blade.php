@if (Auth::user()->isPrincipal())
<div class="modal fade" id="aprobar-documento-modal-dialog" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Validación del Documento</h4>
				<h5 class="modal-title" id="aprobar-documento-modal-document-name"></h5>
			</div>
			<form id="aprobar-documento-modal-dialog-form" class="form-horizontal">
				<div class="modal-body">
					<div class="form-group">
						{{ Form::label('val_id', 'Documento Id', ['class' => 'control-label col-sm-2']) }}
						<div class="col-md-4">
							<input type='text' id="val_id" name="val_id" class="form-control" readonly>
						</div>
						{{ Form::label('val_version', 'Versión', ['class' => 'control-label col-sm-2']) }}
						<div class="col-md-4">
							<input type='text' id="val_version" name="val_version" class="form-control" readonly>
						</div>
					</div>
					{{ Form::fhText('val_tipo_documento', 'Tipo de documento', null, false, 10, null, ['readonly' => true]) }}
					{{ Form::fhText('val_nombre', 'Nombre', null, false, 10, null, ['readonly' => true]) }}
					<div class="form-group">
						{{ Form::label('val_fecha_documento', 'Fecha Documento', ['class' => 'control-label col-sm-2']) }}
						<div class="col-md-4">
							<input type='text' id="val_fecha_documento" class="form-control" readonly>
						</div>
						{{ Form::label('val_fecha_caducidad', 'Fecha de Caducidad', ['class' => 'control-label col-sm-2']) }}
						<div class="col-md-4">
							<input type='text' id="val_fecha_caducidad" class="form-control" readonly>
						</div>
					</div>
					{{ Form::fhText('val_horas_formacion', 'Horas de Formación', null, false, 4, null, ['readonly' => true]) }}
					{{ Form::fhTextarea('val_notas', 'Notas', null, false, 10, null, ['readonly' => true]) }}
					<div id="val_download-file-group" class="form-group">
						{{ Form::label('val_filename', 'Fichero', ['class' => 'control-label col-sm-2']) }}
						<div class="col-sm-4">
							{{ Form::text('val_filename', null, ['class' => 'form-control', 'readonly' => true]) }}
						</div>
						<div class="col-sm-2">
							<a target="_blank" type="button" id="aprobar-documento-modal-descargar-button" class='btn btn-primary'><i class="fa fa-download"></i> &nbsp;Ver/Descargar</a>
						</div>
					</div>
					<hr />
					{{ Form::fhText('val_usuario_validacion', 'Usuario Validación', null, false, 10, Auth::user()->nombre . ' (#' . Auth::user()->id . ')', ['readonly' => true]) }}
					{{ Form::fhText('val_fecha_validacion', 'Fecha Validación', null, false, 4, Date::now()->format('d/m/Y'), ['readonly' => true]) }}
					{{ Form::fhTextarea('val_notas_validacion', 'Notas Validación', 'Use este campo para indicar el motivo de rechazo del documento (obligatorio) o para cualquier otra nota que quiera agregar.') }}
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
					<button type="button" id="aprobar-documento-modal-aprobar-button" class="btn btn-success pull-left"><i class="fa fa-thumbs-up"></i> &nbsp;Aprobar</button>
					<button type="button" id="aprobar-documento-modal-no-aprobar-button" class="btn btn-danger pull-left"><i class="fa fa-thumbs-down"></i> &nbsp;NO Aprobar</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endif
