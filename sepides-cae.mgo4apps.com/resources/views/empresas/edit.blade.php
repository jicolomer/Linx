@extends('layouts.app')

@section('htmlheader_title')
	{{ ($empresa_usuario ? 'Datos de la empresa: ' : 'Empresa: ') . strtoupper($model->razon_social) }}
@endsection

@section('contentheader_title')
	{{ ($empresa_usuario ? 'Datos de la empresa: ' : 'Empresa: ') . strtoupper($model->razon_social) }}
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
							<li><a href="#t2" data-toggle="tab">Documentos</a></li>
							<li><a href="#t3" data-toggle="tab">Trabajadores</a></li>
							<li><a href="#t4" data-toggle="tab">Máquinas</a></li>
						</ul>
						<div class="tab-content">
							<div rol="tabpanel" class="tab-pane fade in active" id="t1">
								{!! Form::model($model, ['route' => ['empresas.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
									{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
									{{ Form::fhText('razon_social', null, 'Razón social o nombre del autónomo', true) }}
									{{ Form::fhText('cif', 'CIF/DNI', null, true, 4) }}
									<hr />
									{{ Form::fhText('direccion', 'Dirección') }}
									{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
									{{ Form::fhText('municipio') }}
									{{ Form::fhSelect('provincia_id', $provincias, $model->provincia_id, 'Provincia', 'Seleccione la provincia...') }}
									<hr />
									{{ Form::fhText('telefono', 'Teléfono', null, false, 4) }}
									{{ Form::fhText('telefono2', 'Teléfono 2', null, false, 4) }}
									{{ Form::fhText('fax', null, null, false, 4) }}
									<hr />
									<div class="form-group{{ $errors->has('codigo_cnae') ? ' has-error' : '' }}">
										{{ Form::label('codigo_cnae', 'Código CNAE', ['class' => 'control-label col-sm-2']) }}
										<div class="col-md-2">
											<input type='text' id="codigo_cnae_c" name="codigo_cnae_c" value="{{ $model->codigo_cnae }}" class="form-control pull-left text-center" disabled>
										</div>
										<div class="col-md-8">
											{{ Form::select('codigo_cnae', $cnaes, $model->codigo_cnae, ['placeholder' => 'Seleccione el código CNAE de la empresa...', 'class' => 'form-control select2']) }}
										</div>
									</div>
									{{ Form::fhSelect('modalidad_preventiva', $modalidades, $model->modalidad_preventiva, null, 'Seleccione modalidad...', true) }}
									<hr />
									{{ Form::fhCheck('construccion', '¿Se dedica a la construccion?', false, $model->construccion) }}
									{{ Form::fhSelect('actividad_construccion', $actividades_construccion, $model->actividad_construccion, 'Actividad de construcción', 'Seleccione actividad...', true) }}
									{{ Form::fhCheck('plantilla_indefinida', '¿Tiene += 30% trabajadores indefinidos?', false, $model->plantilla_indefinida) }}
									{{ Form::fhText('rea', 'Inscripción R.E.A.', null, false, 4) }}
									<hr />
									{{ Form::fhCheck('autonomo', '¿Es Autónomo?', false, $model->autonomo) }}
									{{ Form::fhCheck('trabajadores_a_cargo', '¿Tiene trabajadores a su cargo?', false, $model->trabajadores_a_cargo) }}
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Guardar cambios</button>
										<a href="{{ isset($return_to) ? $return_to : route('empresa') }}" class= "btn btn-default">Cancelar</a>
									</div>
								</form>
							</div>
							<div rol="tabpanel" class="tab-pane fade" id="t2">
								<div id="documentos-box"></div>
							</div>
							<div rol="tabpanel" class="tab-pane fade" id="t3">
								<div class="box-header">
									<h3 class="box-title">Listado de Empleados</h3>
									<div class="box-tools"><a href="{{ route('trabajadores.create') }}?r={{ urlencode(Request::url().'#t3') }}&e={{ $model->id }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a></div>
								</div>
								<div class="box-body">
									<table id="trabajadores-table">
										<thead>
											<tr>
												<th class="text-center">ID</th>
												<th data-priority="1">Apellidos</th>
												<th data-priority="2">Nombre</th>
												<th data-priority="3">NIF/DNI</th>
												<th>Puesto de trabajo</th>
												<th>¿Recurso Preventivo?</th>
												<th>¿Delegado Prevención?</th>
											@if($model->id > 0)
												<th data-priority="4">¿Formación?</th>
												<th data-priority="5">¿Información?</th>
												<th data-priority="4">¿EPIS?</th>
												<th data-priority="5">¿Vigilancia Salud?</th>
												<th data-priority="6">¿Otros?</th>
											@endif
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
							<div rol="tabpanel" class="tab-pane fade" id="t4">
								<div class="box-header">
									<h3 class="box-title">Listado de Máquinas</h3>
									<div class="box-tools"><a href="{{ route('maquinas.create') }}?r={{ urlencode(Request::url().'#t4') }}&e={{ $model->id }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva</a></div>
								</div>
								<div class="box-body">
									<table id="maquinas-table">
										<thead>
											<tr>
												<th class="text-center">ID</th>
												<th data-priority="4">Tipo</th>
												<th data-priority="1">Nombre</th>
												<th data-priority="2">Matrícula</th>
												<th>Marca</th>
												<th>Modelo</th>
												<th>Documentación</th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
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
<script src="{{ asset('/js/empresas.view.js') }}" type="text/javascript"></script>
<script>
$(function() {
	// TRABAJADORES
	var columns = [
		{ data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
		{ data: 'apellidos', name: 'trabajadores.apellidos', cellType: 'th' },
		{ data: 'nombre', name: 'trabajadores.nombre', cellType: 'th', width: '30px' },
		{ data: 'nif', name: 'trabajadores.nif' },
		{ data: 'puesto', name: 'trabajadores.puesto' },
		{ data: 'is_recurso', name: 'is_recurso', orderable: false, searchable: false, width: '10px', className: 'text-center' },
		{ data: 'is_delegado', name: 'is_delegado', orderable: false, searchable: false, width: '10px', className: 'text-center' },
	@if($model->id > 0)
		{ data: 'status_formacion', name: 'status_formacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
		{ data: 'status_informacion', name: 'status_informacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
		{ data: 'status_epis', name: 'status_epis', orderable: false, searchable: false, width: '10px', className: 'text-center' },
		{ data: 'status_salud', name: 'status_salud', orderable: false, searchable: false, width: '10px', className: 'text-center' },
		{ data: 'status_otros', name: 'status_otros', orderable: false, searchable: false, width: '10px', className: 'text-center' },
	@endif
	];
	$.App.DT.set({
		tableName: 'trabajadores-table',
		columnsDef: columns,
		urlList: '{!! route('trabajadores.rowsData') !!}?e={{ $model->id }}',
		urlEdit: '{!! route('trabajadores.edit', 'XX') !!}?r={{ urlencode(Request::url().'#t3') }}',
		addActionColumn: false,
	});
	// MAQUINAS
	columns = [
		{ data: 'id', name: 'maquinas.id', className: 'text-right', cellType: 'th', width: '10px' },
		{ data: 'tipo', name: 'tipo', cellType: 'th' },
		{ data: 'nombre', name: 'maquinas.nombre', cellType: 'th' },
		{ data: 'matricula', name: 'matricula', cellType: 'th' },
		{ data: 'marca', name: 'marca' },
		{ data: 'modelo', name: 'modelo' },
		{ data: 'documentacion', name: 'documentacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
	];
	$.App.DT.set({
		tableName: 'maquinas-table',
		columnsDef: columns,
		urlList: '{!! route('maquinas.rowsData') !!}?e={{ $model->id }}',
		urlEdit: '{!! route('maquinas.edit', 'XX') !!}?r={{ urlencode(Request::url().'#t4') }}',
		addActionColumn: false,
	});
	// DOCUMENTOS
	var doc = $.Documentos();
	doc.init('/empresas');
	doc.createBox('documentos-box', 'Documentos de la Empresa', null, false);
	doc.setupDatatable('documentos-box-table');
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan});
</script>
@endpush
