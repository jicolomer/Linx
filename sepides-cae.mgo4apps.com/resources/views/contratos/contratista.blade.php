@extends('layouts.app')

@section('htmlheader_title')
	Contrato: {{ $contrato->referencia }}
@endsection

@section('contentheader_title')
	Contrato: <strong>{{ $contrato->referencia . "  -  " . $contrato->nombre }}</strong><br />
	{!! $cabecera !!}
@endsection

@section('main-content')
@include('contratos.select-documentos')
@can('contratos.externo')
@include('documentos.modal-form')
@endcan
@can('documentos.validar')
@include('documentos.validation-form')
@endcan
@include('contratos.add-trabajadores-maquinas')
<div class="row">
	<div class="col-xs-12">
		<div class="box">
			<div class="box-body">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
					@if ($subcontratista == false)
						<li class="active"><a href="#t1" data-toggle="tab">C.A.E.</a></li>
						<li><a href="#t2" data-toggle="tab">Documentación &nbsp; {!! $status_doc !!}</a></li>
					@else
						<li class="active"><a href="#t2" data-toggle="tab">Documentación &nbsp; {!! $status_doc !!}</a></li>
					@endif
						<li><a href="#t3" data-toggle="tab">Trabajadores &nbsp; {!! $status_doc_trabajadores !!}</a></li>
						<li><a href="#t4" data-toggle="tab">Máquinas &nbsp; {!! $status_doc_maquinas !!}</a></li>
					</ul>
					<div class="tab-content">
					@if ($subcontratista == false)
						<div class="tab-pane active" id="t1">
							<div class="row">
								<div class="col-md-10 col-md-offset-1">
									<h3 class="box-title">Subcontratistas</h3>
									<div class="box box-primary">
										<div class="box-body">
											<div class="table-responsive">
												<table id="subcontratistas-table">
													<thead>
														<tr>
															<th class="text-center">ID</th>
															<th>Razón Social</th>
															<th>Contacto</th>
															<th>Doc. Empresa</th>
															<th>Doc. Trabajadores</th>
															<th>Doc. Máquinas</th>
														</tr>
													</thead>
												</table>
											</div>
										</div>
									</div>
          						</div>
          					</div>
						</div>
						<div class="tab-pane" id="t2">
					@else
						<div class="tab-pane active" id="t2">
					@endif
							<div id="doc-faltante"></div>
							<div id="documentacion"></div>
						</div>
						<div class="tab-pane" id="t3">
							<div id="trabajadores"></div>
						</div>
						<div class="tab-pane" id="t4">
							<div id="maquinas"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/plugins/modalLoading/modalLoading.js') }}"></script>
@can('contratos.externo')
<script src="{{ asset('/js/documentos.js') }}" type="text/javascript"></script>
@endcan
@can('documentos.validar')
<script src="{{ asset('/js/documentos-validation.js') }}" type="text/javascript"></script>
@endcan
<script src="{{ asset('/js/contratos.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/add-trabajadores-maquinas.js') }}" type="text/javascript"></script>
<script>
$(function() {
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan
@if ($subcontratista == false)
	// Tabla SUBCONTRATISTAS
	var columns = [
		{ data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
		{ data: 'razon_social', name: 'razon_social', cellType: 'th' },
		{ data: 'contacto', name: 'contacto', orderable: false },
		{ data: 'status_doc', name: 'status_doc', className: 'text-center vcenter' },
		{ data: 'status_trabajadores', name: 'status_trabajadores', className: 'text-center vcenter' },
		{ data: 'status_maquinas', name: 'status_maquinas', className: 'text-center vcenter' },
	];
	var dt_sub = $.App.DT.set({
		tableName: 'subcontratistas-table',
		columnsDef: columns,
		urlList: "{!! route('contratos.contratistasData') . '?c=' . $empresa_id !!}",
		urlEdit: "{!! route('contratos.subcontratista', [$contrato->id, $empresa_id, 'XX']) !!}",
		addActionColumn: false,
	});
@endif
	// Documentación
	var $Contratos = $.Contratos();
	var docFaltanteDT = $Contratos.setupDocFaltante(
		"doc-faltante",
		"del Contratista",
		'a=CTA&c={{ $empresa_id }}',
		{{ Auth::user()->isExterno() ? 'true' : 'false' }}
	);
	$Contratos.setupDocContrato({
		containerName: 'documentacion',
		textTitle: 'del Contratista',
		urlFilter: 'a=CTA&c={{ $empresa_id }}',
		edit: {{ Auth::user()->isExterno() ? 'true' : 'false' }},
		showAmbito: false,
		docFaltanteDT: docFaltanteDT,
	});
@can('contratos.externo')
	// Añadir documento
	$('#documentos-modal-dialog').on('hidden.bs.modal', function () {
		$Contratos.getSelectionDatatable().ajax.reload();
    });
	$.Documentos().init('{!! Request::url() !!}');
@endcan
	// Trabajadores / Máquinas
	$.AddTrabajadoresMaquinas().init({
		allowAdd: {{ Auth::user()->isExterno() ? 'true' : 'false' }},
		trabajadoresDataUrl: '{!! route('contratos.trabajadoresData') !!}',
		maquinasDataUrl: '{!! route('contratos.maquinasData') !!}',
		selectTrabajadoresUrl: '{!! route('trabajadores.rowsData') . '?e=' . $empresa_id !!}',
		selectMaquinasUrl: '{!! route('maquinas.rowsData') . '?e=' . $empresa_id !!}',
		selectCentrosUrl: '{!! route('contratos.centrosData') !!}',
		fechaInicio: '{{ $contrato->fecha_inicio_obras }}',
		fechaFin: '{{ $contrato->fecha_fin_obras }}',
@can('acceso.update')
		updateAccess: true,
@endcan
	});
});
</script>
@endpush
