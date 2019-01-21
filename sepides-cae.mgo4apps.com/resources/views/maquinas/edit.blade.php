@extends('layouts.app')

@section('htmlheader_title')
	Datos de la máquina
@endsection

@section('contentheader_title')
	Datos de la máquina
@endsection

@section('main-content')
	@include('documentos.modal-form')
@can('documentos.validar')
	@include('documentos.validation-form')
@endcan
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#t1" data-toggle="tab" >General</a></li>
							<li><a href="#t2" data-toggle="tab">Documentación</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="t1">
								{!! Form::model($model, ['route' => ['maquinas.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
									{{ Form::fhText('id', 'ID máquina', null, false, 4, $model->id, ['readonly' => true]) }}
									{{ Form::hidden('empresa_id', $model->empresa_id) }}
									{{ Form::fhText('empresa_nombre', 'Empresa', null, true, 4, $empresa_nombre, ['readonly' => 'true']) }}
									<hr />
									{{ Form::fhSelect('tipo_maquina_id', $tipos_maquinas, $model->tipo_maquina_id, 'Tipo de máquina', 'Tipo de máquina...', true) }}
									{{ Form::fhText('nombre', null, null, true) }}
									{{ Form::fhText('marca') }}
									{{ Form::fhText('modelo') }}
									<hr />
									{{ Form::fhText('matricula', 'Matrícula', null, true, 4) }}
									{{ Form::fhText('num_serie', 'Número de serie', null, false, 4) }}
									{{ Form::fhText('num_bastidor', 'Número de bastidor', null, false, 4) }}
									<hr />
									<div id="anio_fabricacion_group" class="required form-group{{ $errors->has('anio_fabricacion') ? ' has-error' : '' }}">
										{{ Form::label('anio_fabricacion', 'Año de fabricación', ['class' => 'control-label col-sm-2']) }}
										<div class="col-md-4">
											{{ Form::number('anio_fabricacion', $model->anio_fabricacion, ['class' => 'form-control']) }}
										</div>
										<div class="col-md-2">
											<span id="anio_fabricacion_text" class="badge bg-orange" style="margin-top:8px"></span>
										</div>
									</div>
									<hr />
									{{ Form::fhTextarea('notas') }}
									{{ Form::fhDate('fecha_alta', null, true, $model->fecha_alta) }}
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Guardar cambios</button>
										<button type="button" class="btn btn-danger pull-right"><i class="fa fa-times"></i> Dar de baja</button>
									</div>
								</form>
							</div>
							<div class="tab-pane" id="t2">
								<div id="doc-faltante" class="box box-danger">
									<div class="box-header">
										<h3 class="box-title">Documentación requerida para: <strong>{{ \App\Models\TipoMaquina::find($model->tipo_maquina_id)->nombre }}</strong></h3>
									</div>
									<div class="box-body">
										<table id="doc-faltante-table" class="table table-bordered table-hover">
											<thead>
												<tr>
													<th class="text-right">ID</th>
													<th>Referencia</th>
													<th>Nombre</th>
													<th>Ámbito</th>
													<th>Caducidad</th>
													<th>Palabras clave</th>
													<th>¿Obligatorio?</th>
													<th></th>
												</tr>
											</thead>
											<tbody></tbody>
										</table>
									</div>
								</div>
								<hr />
								<div id="documentos-box"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
@can('documentos.validar')
<script src="{{ asset('/js/documentos-validation.js') }}" type="text/javascript"></script>
@endcan
<script src="{{ asset('/js/documentos.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/maquinas.view.js') }}" type="text/javascript"></script>
<script>
$(function() {
	var $doc_faltante = $('#doc-faltante');
	$doc_faltante.hide();

	var doc = $.Documentos();
	doc.init('/maquinas');
	doc.createBox('documentos-box', 'Documentos de la Máquina', 'success');
	doc.setupDatatable('documentos-box-table');
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan

	var dt = $('#doc-faltante-table').DataTable({
		ajax: "{!! route('maquinas.docFaltanteData') !!}",
		columns: [
			{ data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
			{ data: 'referencia', name: 'referencia', cellType: 'th' },
			{ data: 'nombre', name: 'nombre', cellType: 'th' },
			{ data: 'ambito', name: 'ambito' },
			{ data: 'tipo_caducidad', name: 'tipo_caducidad' },
			{ data: 'tags', name: 'tags' },
			{ data: 'obligatorio', name: 'obligatorio', className: 'text-center', orderable: false, searchable: false },
			{ data: 'actions', name: 'actions', className: 'text-center', orderable: false, searchable: false, width: '80px' }
		],
		filter: false,
		lengthChange: false,
		paginate: false,
		info: false,
	});
	dt.on('draw', function(o, data) {
		var d = data['json'];
		if (d && (d.recordsFiltered > 0)) {
			$doc_faltante.fadeIn();
		} else {
			$doc_faltante.fadeOut();
		}
	});
});
</script>
@endpush
