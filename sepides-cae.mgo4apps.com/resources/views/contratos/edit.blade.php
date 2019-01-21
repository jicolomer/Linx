@extends('layouts.app')

@section('htmlheader_title')
	Contrato: {{ $model->referencia }}
@endsection

@section('contentheader_title')
	Contrato: <strong>{{ $model->referencia . "  -  " . $model->nombre }}</strong>
@endsection

@section('main-content')
	@include('centros.select-centros')
	@include('tipos_documentos.select-tipos-documentos')
	@include('contratos.add-contratistas')
	@include('contratos.select-documentos')
@can('contratos.update')
	@include('documentos.modal-form')
@endcan
@can('documentos.validar')
	@include('documentos.validation-form')
@endcan
<?php
	$contratista = Auth::user()->isExterno();
	$canUpdate = Auth::user()->can('contratos.update');
?>
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
						<li class="active">
							<a href="#t1" data-toggle="tab">
								C.A.E. &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Desde aquí se realiza la coordinación del Contrato"></i>
							</a>
						</li>
						<li>
							<a href="#t2" data-toggle="tab" id="datos-contrato-tab">
								Datos Contrato &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Datos generales del Contrato"></i>
							</a>
						</li>
					@if (! $contratista)
						<li>
							<a href="#t0" data-toggle="tab" id="datos-contrato-tab">
								<span class="text-danger">Área Privada &nbsp; </span>
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Notas y documentos privados del contrato"></i>
							</a>
						</li>
					@endif
					@if ($num_centros > 0)
						<li>
							<a href="#t3" data-toggle="tab">
								Centros &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Centros de Trabajo cubiertos por el Contrato"></i>
							</a>
						</li>
					@endif
					@if ($model->tipo_contrato_id != null)
						<li>
							<a href="#t4" data-toggle="tab">
								Doc. Requerida &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Documentación requerida para el contrato (Empresa Principal, contratistas, trabajadores y maquinaria)"></i>
							</a>
						</li>
					@endif
					@if ($model->tipo_contrato_id != null)
						<li>
							<a href="#t5" data-toggle="tab">
								Doc. Empresa Principal &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Documentación que aporta la Empresa Principal al Contrato"></i>
							</a>
						</li>
					@endif
					@if ($contratistas_contrato->count() > 0)
						<li>
							<a href="#t6" data-toggle="tab">
								Contratistas &nbsp;
								<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Listado de los Contratistas y Subcontratistas (si los hubiere)"></i>
							</a>
						</li>
					@endif
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="t1">
							<h2 class="page-header">Empresa Principal</h2>
							<div class="row">
            					<div class="col-md-6">
              						<div class="box box-success">
                						<div class="box-header with-border">
                  							<h3 class="box-title"><span class="label text-green"><i class="fa fa-check"></i></span>Alta del Contrato &nbsp;<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Alta inicial del contrato por parte de la empresa principal"></i></h3>
                  							<div class="box-tools pull-right">
												<span class="label text-black">{{ Date::parse($model->created_at)->format('d/m/Y H:i') }}</span>
                    							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  							</div>
                						</div>
                						<div class="box-body">
											<form class="form-horizontal">
												<div class="form-group">
													<label class="control-label col-sm-4">Responsable del Contrato</label>
													<div class="col-sm-8">
														<div class="input-group">
															<input class="form-control" readonly="1" type="text" value="{{ $responsable_contrato_nombre }}">
															<span class="input-group-addon" style="background-color:#eee">
																<i class="fa fa-address-book text-dark-gray" data-toggle="tooltip" title="{{ $responsable_contrato_contacto }}"></i>
															</span>
														</div>
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4">Técnico P.R.L.</label>
													<div class="col-sm-8">
														<div class="input-group">
															<input class="form-control" readonly="1" type="text" value="{{ $tecnicoprl_nombre }}">
															<span class="input-group-addon" style="background-color:#eee">
																<i class="fa fa-address-book text-dark-gray" data-toggle="tooltip" title="{{ $tecnicoprl_contacto }}"></i>
															</span>
														</div>
													</div>
												</div>
											</form>
                						</div>
              						</div>
            					</div>
							@if ($model->tipo_contrato_id == null)
								<div class="col-md-6">
              						<div class="box box-danger">
                						<div class="box-header with-border">
                  							<h3 class="box-title"><span class="label text-red"><i class="fa fa-times"></i></span>Tipo de Contrato</h3>
                						</div>
                						<div class="box-body">
											<div class="col-sm-10 col-sm-offset-1">
												<h4 class="text-red">Aún no se ha especificado el <strong>Tipo de Contrato</strong>.</h4>
												Hasta que no se haya indicado el tipo de este contrato,
												<strong>no estará disponible (ni se podrá añadir)</strong> la <em>documentación requerida</em>,
												la documentación de la <em>empresa principal</em> y sus <em>centros de trabajo</em>,
												la de los <em>contratistas/subcontratistas</em>, así como la de sus
												<em>trabajadores</em> y <em>máquinas</em>.<br />
												<br />
											</div>
										@can('contratos.update')
											<div class="col-sm-12 text-center">
												<button id="set_tipo_contrato_id_button" type="button" class="btn btn-danger">Especificar Tipo de Contrato</button>
											</div>
										@endcan
                						</div>
              						</div>
            					</div>
							@else
								<div class="col-md-6">
              						<div class="box box-success">
                						<div class="box-header with-border">
                  							<h3 class="box-title"><span class="label text-{{ ($num_centros > 0) ? "green" : "red" }}"><i class="fa fa-{{ ($num_centros > 0) ? "check" : "times" }}"></i></span>Documentación Requerida &nbsp;<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Documentación requerida para este contrato"></i></h3>
                  							<div class="box-tools pull-right">
                    							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  							</div>
                						</div>
                						<div class="box-body">
											<form class="form-horizontal">
												<div class="form-group">
													<label class="control-label col-sm-4">Doc. obligatorios</label>
													<div class="col-sm-4">
														<input id="doc-obligatorios-count" class="form-control" readonly="1" type="text" value="0">
													</div>
													<div class="col-sm-4">
													@can('contratos.update')
														<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#tipos-documentos-modal-dialog">Añadir &nbsp;<i class="fa fa-plus"></i></button>
													@endcan
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4">Doc. NO obligatorios</label>
													<div class="col-sm-4">
														<input id="doc-no-obligatorios-count" class="form-control" readonly="1" type="text" value="0">
													</div>
												</div>
											</form>
                						</div>
              						</div>
            					</div>
							@endif
          					</div>
							<div class="row">
            					<div class="col-md-6">
              						<div class="box box-{{ ($num_centros > 0) ? "success" : "danger" }}">
                						<div class="box-header with-border">
                  							<h3 class="box-title"><span class="label text-{{ ($num_centros > 0) ? "green" : "red" }}"><i class="fa fa-{{ ($num_centros > 0) ? "check" : "times" }}"></i></span>Centros de Trabajo &nbsp;<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Se deben añadir los Centros de Trabajo de la empresa principal afectos al contrato"></i></h3>
                  							<div class="box-tools pull-right">
                    							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  							</div>
                						</div>
                						<div class="box-body">
											<form class="form-horizontal">
												<div class="form-group">
													<label class="control-label col-sm-4">Centros añadidos</label>
													<div class="col-sm-4">
														<input id="centros-count" class="form-control" readonly="1" type="text" value="{{ $num_centros }}">
													</div>
													<div class="col-sm-4">
													@can('contratos.update')
														<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#centros-modal-dialog">Añadir <i class="fa fa-plus"></i></button>
													@endcan
													</div>
												</div>
												<div class="form-group">
													<label class="control-label col-sm-4">Documentación centros</label>
													<div class="col-sm-4">
														<i class="fa fa-lg fa-ban text-gray"></i>
													</div>
												</div>
											</form>
                						</div>
              						</div>
            					</div>
            					<div class="col-md-6">
              						<div class="box box-{{ ($contratistas_contrato->count() > 0) ? "success" : "danger" }}">
                						<div class="box-header with-border">
                  							<h3 class="box-title"><span class="label text-{{ ($contratistas_contrato->count() > 0) ? "green" : "red" }}"><i class="fa fa-{{ ($contratistas_contrato->count() > 0) ? "check" : "times" }}"></i></span>Contratistas &nbsp;<i class="fa fa-question-circle text-dark-gray" data-toggle="tooltip" title="Empresas Contratistas y Subcontratistas (si los hubiese) de este contrato"></i></h3>
                  							<div class="box-tools pull-right">
                    							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                  							</div>
                						</div>
                						<div class="box-body">
											<form class="form-horizontal">
												<div class="form-group">
													<label class="control-label col-sm-4">Contratistas del contrato</label>
													<div class="col-sm-4">
														<input class="form-control" readonly="1" type="text" value="{{ $contratistas_contrato->count() }}">
													</div>
												@can('contratos.add-contratistas')
													<div class="col-sm-4">
														<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#add-contratista-wizard">Añadir <i class="fa fa-plus"></i></button>
													</div>
												@endcan
												</div>
												<div class="form-group">
													<label for="num_contratistas" class="control-label col-sm-4">Subcontratistas del contrato</label>
													<div class="col-sm-4">
														<input class="form-control" readonly="1" type="text" value="{{ $num_subcontratistas }}">
													</div>
												@if (($contratistas_contrato->count() > 0) && Auth::user()->can('contratos.externo'))
													<div class="col-sm-4">
														<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#add-contratista-wizard" data-contratista="{{ Auth::user()->empresa_id }}">Añadir <i class="fa fa-plus"></i></button>
													</div>
												@endif
												</div>
											</form>
                						</div>
              						</div>
            					</div>
          					</div>
						</div>
						<div class="tab-pane" id="t2">
							{!! Form::model($model, ['route' => ['contratos.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
								{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
							@if (($model->tipo_contrato_id == null) && $canUpdate)
								<div class="form-group has-error required" id="tipo_contrato_id-group">
									{{ Form::label('tipo_contrato_id', 'Tipo de contrato', ['class' => 'control-label col-sm-2']) }}
									<div class="col-sm-10">
										{{ Form::select('tipo_contrato_id', $tipos, null, ['placeholder' => 'Seleccione un tipo de contrato...', 'class' => 'form-control select2']) }}
									</div>
								</div>
							@else
								<input type="hidden" name="tipo_contrato_id" value="{{ $model->tipo_contrato_id }}">
								{{ Form::fhText('tipo_contrato', 'Tipo de contrato', null, true, 4, ($model->tipo_contrato_id == null ? null : $tipos[$model->tipo_contrato_id]), ['readonly' => true]) }}
							@endif
								{{ Form::fhText('referencia', null, null, true, 4, null, $canUpdate ? [] : ['readonly' => true]) }}
								{{ Form::fhText('nombre', null, null, true, 10, null, $canUpdate ? [] : ['readonly' => true]) }}
								<hr />
								{{ $model->fecha_firma }}
								{{ Form::fhDate('fecha_firma', null, false, ($model->fecha_firma ? $model->fecha_firma : '-'), $canUpdate ? [] : ['readonly' => true]) }}
								{{ Form::fhDate('fecha_inicio_obras', 'Fecha Inicio Contrato', false, ($model->fecha_inicio_obras ? $model->fecha_inicio_obras : '-'), $canUpdate ? [] : ['readonly' => true]) }}
								{{ Form::fhDate('fecha_fin_obras', 'Fecha Fin Contrato', false, ($model->fecha_fin_obras ? $model->fecha_fin_obras : '-'), $canUpdate ? [] : ['readonly' => true]) }}
								<hr />
							@if (! $canUpdate)
								{{ Form::fhText('responsable_contrato_id', 'Responsable del Contrato', null, true, 4, $responsable_contrato_nombre, ['readonly' => true]) }}
								{{ Form::fhText('tecnico_encargado_id', 'Técnico Encargado', null, false, 4, App\Models\Trabajador::getNombreTrabajador($model->tecnico_encargado_id), ['readonly' => true]) }}
								{{ Form::fhText('tecnico_encargado2_id', 'Técnico Encargado 2', null, false, 4, App\Models\Trabajador::getNombreTrabajador($model->tecnico_encargado2_id), ['readonly' => true]) }}
								<hr />
								{{ Form::fhText('tecnico_prl_id', 'Técnico P.R.L.', null, true, 4, $tecnicoprl_nombre, ['readonly' => true]) }}
								{{ Form::fhText('coordinador_cap_id', 'Coordinador C.A.P.', null, false, 4, App\Models\Trabajador::getNombreTrabajador($model->coordinador_cap_id), ['readonly' => true]) }}
								{{ Form::fhText('tecnico_averias_id', 'Técnico Averías', null, false, 4, App\Models\Trabajador::getNombreTrabajador($model->tecnico_averias_id), ['readonly' => true]) }}
							@else
								{{ Form::fhSelect('responsable_contrato_id', $responsables, $model->responsable_contrato_id, 'Responsable del Contrato', 'Seleccione el empleado...', true) }}
								{{ Form::fhSelect('tecnico_encargado_id', $responsables, $model->tecnico_encargado_id, 'Técnico Encargado', 'Seleccione el empleado...', false) }}
								{{ Form::fhSelect('tecnico_encargado2_id', $responsables, $model->tecnico_encargado2_id, 'Técnico Encargado 2', 'Seleccione el empleado...', false) }}
								<hr />
								{{ Form::fhSelect('tecnico_prl_id', $tecnicos, $model->tecnico_prl_id, 'Técnico P.R.L.', 'Seleccione el empleado...', true) }}
								{{ Form::fhSelect('coordinador_cap_id', $tecnicos, $model->coordinador_cap_id, 'Coordinador C.A.P.', 'Seleccione el empleado...', false) }}
								{{ Form::fhSelect('tecnico_averias_id', $tecnicos, $model->tecnico_averias_id, 'Técnico Averías', 'Seleccione el empleado...', false) }}
							@endif
								<hr />
								{{ Form::fhTextarea('notas', null, '', false, 10, null, $canUpdate ? [] : ['readonly' => true]) }}
								<div class="box-footer">
								@can('contratos.update')
									<button type="submit" class="btn btn-primary">Guardar cambios</button>
								@endcan
								@can('contratos.delete')
									<button id="remove_contrato_button" type="button" class="btn btn-danger pull-right"><i class="fa fa-times"></i> Dar de baja</button>
								@endcan
								</div>
							</form>
						</div>
					@if (! $contratista)
						@include('contratos.add-documento-privado')
						<div class="tab-pane" id="t0">
							{!! Form::model($model, ['route' => ['contratos.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
								{{ Form::fhNumber('importe_contrato', 'Importe Contrato', false, null, $canUpdate ? ['step' => 'any'] : ['readonly' => true, 'step' => 'any']) }}
								<hr />
								{{ Form::fhTextarea('notas_privadas', null, '', false, 10, null, $canUpdate ? [] : ['readonly' => true]) }}
								<hr />
								<h4>Documentos privados</h4>
							<?php $idx = 0; $count = 0; ?>
							@foreach($doc_privados as $doc)
							@if (file_exists($doc->getPath('thumb')))
								@if (($count % 3) == 0)
								<div class="row">
								@endif
								<?php $image_data = base64_encode(file_get_contents($doc->getPath('thumb'))); ?>
									<a href="{{ route('contratos.getDocumentoPrivado', $idx) }}" target="_blank">
										<div class="col-sm-4 text-center">
											<h5>{{ $doc->name }}</h5>
											<img src="data: image/jpeg;base64,{{ $image_data }}">
										</div>
									</a>
								@if ((($count+1) % 3) == 0)
								</div>
								@endif
								<?php $count++; ?>
							@endif
							<?php $idx++; ?>
							@endforeach
							@if (($count % 3) != 0)
								</div>
							@endif
								<div class="box-footer">
								@can('contratos.update')
									<button type="submit" class="btn btn-primary">Guardar cambios</button>
									<button type="button" class="btn btn-warning pull-right bootstrap-modal-form-open" data-toggle="modal" data-target="#add-documento-privado-dialog">Añadir documento</button>
								@endcan
								</div>
							</form>
						</div>
					@endif
					@if ($num_centros > 0)
						<div class="tab-pane" id="t3">
							<div class="box-header">
								<h3 class="box-title">Centros de trabajo cubiertos por el contrato</h3>
								<div class="box-tools">
								@if ($canUpdate)
									<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#centros-modal-dialog"><i class="fa fa-plus"></i> Añadir</button>
								@endif
								</div>
							</div>
							<div class="box-body">
								<div class="table-responsive">
									<table id="centros-table">
										<thead>
											<tr>
												<th class="text-center">ID</th>
												<th>Nombre</th>
												<th>Código Postal</th>
												<th>Municipio</th>
												<th>Email Centro</th>
												<th>Teléfono Centro</th>
												<th>Persona Contacto</th>
												<th>Teléfono Contacto</th>
												<th>Email Contacto</th>
											@if ($canUpdate)
												<th data-priority="1"></th>
											@endif
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
					@endif
					@if ($model->tipo_contrato_id != null)
						<div class="tab-pane" id="t4">
							<div class="box-header">
								<h3 class="box-title">Documentación requerida para este contrato</h3>
								<div class="box-tools">
								@can('contratos.update')
									<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#tipos-documentos-modal-dialog">Añadir &nbsp;<i class="fa fa-plus"></i></button>
								@endcan
								</div>
							</div>
							<div class="box-body">
								<div class="table-responsive">
									<table id="documentacion-requerida-table">
										<thead>
											<tr>
												<th class="text-right">ID</th>
												<th>Referencia</th>
												<th>Nombre</th>
												<th>Ámbito</th>
												<th>Tipo Caducidad</th>
												<th>Palabras Clave</th>
												<th>¿Obligatorio?</th>
											@if ($canUpdate)
												<th></th>
											@endif
											</tr>
										</thead>
									</table>
								</div>
							</div>
						</div>
					@endif
					@if ($model->tipo_contrato_id != null)
						<div class="tab-pane" id="t5">
						@if (! $contratista)
							<div id="doc-faltante-ppal"></div>
						@endif
							<div id="documentacion-ppal"></div>
						</div>
					@endif
					@if ($contratistas_contrato->count() > 0)
						<div class="tab-pane" id="t6">
							<div id="contratistas"></div>
						</div>
					@endif
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/plugins/modalLoading/modalLoading.js') }}"></script>
<script src="{{ asset('/js/select-centros.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/select-tipos-documentos.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/add-contratistas.js') }}" type="text/javascript"></script>
@can('contratos.update')
<script src="{{ asset('/js/documentos.js') }}" type="text/javascript"></script>
@endcan
@can('documentos.validar')
<script src="{{ asset('/js/documentos-validation.js') }}" type="text/javascript"></script>
@endcan
<script src="{{ asset('/js/contratos.js') }}" type="text/javascript"></script>
<script>
$(function() {
@if (($model->tipo_contrato_id == null) && $canUpdate)
	$.Contratos().setupTipoContratoSelect();
	$('#set_tipo_contrato_id_button').on('click', function () {
		$("#datos-contrato-tab").tab('show');
		$("#tipo_contrato_id").select2('open');
	});
@endif
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan
	// CENTROS
	var centrosDT = $.SCT().init(
		"/contratos",
		"{!! route('contratos.centrosData') !!}",
	@if ($contratista)
		null,
	@else
		"{!! route('centros.edit', 'XX') !!}?r={{ urlencode(Request::url().'#t4') }}",
	@endif
		"{!! route('centros.rowsData') !!}",
		{{ $canUpdate ? 'true' : 'false' }}
	);
	centrosDT.on('draw.dt', function(e, dt) {
		if (dt.json) {
			$('#centros-count').val(dt.json.recordsTotal);
		}
	});
	// TIPOS DOCUMENTOS REQUERIDOS
	var docRequeridaDT = $.STD().init(
		"/contratos",
		'{!! route('tipos-documentos.rowsData') !!}',
		'documentacion-requerida-table',
		'{!! route('contratos.tiposDocumentosData') !!}',
		'el Tipo de Documento requerido',
		{{ $canUpdate ? 'true' : 'false' }}
	);
	docRequeridaDT.on('draw.dt', function(e, dt) {
		if (dt.json) {
			var ob = 0;
			var nob = 0;
			$.each(dt.json.data, function(idx, doc) {
				if (doc.obligatorio) {
					ob++;
				} else {
					nob++;
				}
			});
			$('#doc-obligatorios-count').val(ob);
			$('#doc-no-obligatorios-count').val(nob);
		}
	});
	// SUBCONTRATISTAS
@foreach ($contratistas_contrato as $id => $nombre)
@if (($contratista && ((Auth::user()->empresa_id == $id) || ($model->is_subcontratista_of(Auth::user()->empresa_id, $id)))) || !$contratista)
	$('#t6').append('<div id="subcontratistas_{{ $id }}" class="subcontratistas_box" data-name="{{ $nombre }}"></div>');
@endif
@endforeach
	// TABLA CONTRATISTAS
	$.AddContratistas().init(
		"/contratos",
		"{!! route('trabajadores.empresaUsuarios', 'XX') !!}",
		"{!! ($contratistas_contrato->count() > 0) ? route('contratos.contratistasData') : null !!}",
	@if ($model->tipo_contrato_id != null)
		"{!! route('contratos.contratista', [$model->id, 'XX']) !!}?r={{ urlencode(Request::url().'#t6') }}",
	@else
		null,
	@endif
		{{ $contratista ? 'true' : 'false' }},
		{{ Auth::user()->can('contratos.add-contratistas') ? 'true' : 'false' }},
		{{ config('cae.invitar_subcontratistas') ? 'true' : 'false' }}
	);
	// Doc. Emp. Principal
	var $Contratos = $.Contratos();
@if (! $contratista)
	var docFaltanteDT = $Contratos.setupDocFaltante("doc-faltante-ppal", "de la Empresa Principal", 'a=EMP', {{ $canUpdate ? 'true' : 'false' }});
@endif
	$Contratos.setupDocContrato({
		containerName: 'documentacion-ppal',
		textTitle: 'de la Empresa Principal',
		urlFilter: 'a=EMP',
		edit: {{ $canUpdate ? 'true' : 'false' }},
		showCaducidad: {{ $contratista ? 'false' : 'true' }},
		showValidacion: {{ $contratista ? 'false' : 'true' }},
		buttonsInActionColumn: {{ $canUpdate ? '3' : ($contratista ? '1' : '2') }},
@if (! $contratista)
		docFaltanteDT: docFaltanteDT,
@endif
	});

@can('contratos.update')
	var $modalDocumento = $('#documentos-modal-dialog');
	var $tipoDocSel = $('#tipo_documento_id');
	var $tipoDocAmbito = $('#tipo_documento_ambito');
    var $centroGroup = $('#centro_id_documento-group');

	var centrosSel = $('{!! $centros_sel_html !!}');
	$('input#tipo_documento_ambito').after(centrosSel);
    var $centroSel = $('#centro_id_documento');
	$centroSel.prop('disabled', true);
	loadCentros();

	$tipoDocAmbito.on('change', function() {
		var ambito = $tipoDocAmbito.val();
		$centroSel.prop('disabled', (ambito != 'CEN'));
	});
	$modalDocumento.on('hidden.bs.modal', function () {
		$Contratos.getSelectionDatatable().ajax.reload();
    });
	function loadCentros() {
			var url = '{!! route('contratos.centrosData') !!}?s=true';
            $.getJSON(url, function(d) {
                if (d) {
                    $centroSel.select2({
                        data: d,
                        placeholder: 'Seleccione Centro...',
                        width: '100%',
                        allowClear: true,
                        language: 'es',
                        dropdownParent: $modalDocumento,
                    });
                }
            });
        }
	$.Documentos().init('/contratos');
@endcan
@can('contratos.delete')
	// Remove Contrato
	$('#remove_contrato_button').on('click', function(e) {
		var msg = '¿Está seguro de querer <strong>dar de baja</strong> el contrato con Ref.: <strong>\'{{ $model->referencia }}\'</strong>?<br /><br />';
		msg += 'Los <em>trabajadores y la maquinaria</em> que tuviesen permiso de acceso a los Centros de trabajo (en virtud de este contrato) lo perderán.'
		bootbox.dialog({
			title: "Por favor, confirme",
			message: msg,
			className: "modal-danger",
			onEscape: function() {},
			buttons: {
				si: {
					label: "Sí, quiero darlo de baja",
					className: "btn-outline pull-right",
					callback: function() {
						var route = "{{ route('contratos.remove', $model->id) }}";
						$.post(route, function(d) {
							if (d != null) {
								if (d.result != null) {
									if (d.result == 'success') {
										location = "{{ route('contratos.index') }}";
										return true;
									}
								}
							}
							bootbox.alert("Ha ocurrido un error y no se ha podido dar de baja el contrato.")
						});
					}
				},

				no: {
					label: "Cancelar",
					className: "btn-outline pull-left",
				},
			}
		});
	});
@endcan
	$('#responsable_contrato_id-group').append('<div class="col-sm-6"><input value="{{ $responsable_contrato_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
	$('#tecnico_encargado_id-group').append('<div class="col-sm-6"><input value="{{ $tecnico_encargado_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
	$('#tecnico_encargado2_id-group').append('<div class="col-sm-6"><input value="{{ $tecnico_encargado2_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
	$('#tecnico_prl_id-group').append('<div class="col-sm-6"><input value="{{ $tecnicoprl_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
	$('#coordinador_cap_id-group').append('<div class="col-sm-6"><input value="{{ $coordinador_cap_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
	$('#tecnico_averias_id-group').append('<div class="col-sm-6"><input value="{{ $tecnico_averias_contacto }}" type="text" class="form-control" readonly="readonly"/></div>');
});
</script>
@endpush
