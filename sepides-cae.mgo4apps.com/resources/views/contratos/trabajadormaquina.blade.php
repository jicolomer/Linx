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
						<li class="active"><a href="#t1" data-toggle="tab">C.A.E.</a></li>
						<li><a href="#t2" data-toggle="tab">Documentación &nbsp; {!! $status_doc !!}</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active" id="t1">
							<div id="{{ $isTrabajador ? 'trabajadores' : 'maquinas' }}"></div>
						</div>
						<div class="tab-pane" id="t2">
							<div id="doc-faltante"></div>
							<div id="documentacion"></div>
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
	// Documentación
	var $Contratos = $.Contratos();
	var docFaltanteDT = $Contratos.setupDocFaltante(
		'doc-faltante',
		'{{ $isTrabajador ? 'del Trabajador' : 'de la Máquina' }}',
		'a={{ $isTrabajador ? 'TRA' : 'MAQ' }}&{{ $isTrabajador ? 't' : 'm' }}={{ $item_id }}',
		{{ Auth::user()->can('contratos.externo') ? 'true' : 'false' }}
	);
	$Contratos.setupDocContrato({
		containerName: 'documentacion',
		textTitle: '{{ $isTrabajador ? 'del Trabajador' : 'de la Máquina' }}',
		urlFilter: 'a={{ $isTrabajador ? 'TRA' : 'MAQ' }}&{{ $isTrabajador ? 't' : 'm' }}={{ $item_id }}',
		edit: {{ Auth::user()->can('contratos.externo') ? 'true' : 'false' }},
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
		itemId: {{ $item_id }},
		itemName: '{{ $item_name }}',
@if($isTrabajador)
		trabajadoresDataUrl: '{!! route('contratos.trabajadoresData') !!}?t={{ $item_id }}',
@else
		maquinasDataUrl: '{!! route('contratos.maquinasData') !!}?m={{ $item_id }}',
@endif
		selectCentrosUrl: '{!! route('contratos.centrosData') !!}',
		fechaInicio: '{{ $contrato->fecha_inicio_obras }}',
		fechaFin: '{{ $contrato->fecha_fin_obras }}',
	});
});
</script>
@endpush
