@extends('layouts.app')

@section('htmlheader_title')
	Nuevo contrato
@endsection

@section('contentheader_title')
	Nuevo contrato
@endsection

@section('main-content')
			{{ Form::fhOpen('contratos.store', 'Datos del nuevo contrato') }}
				{{ Form::fhSelect('tipo_contrato_id', $tipos, null, 'Tipo de contrato', 'Seleccione un tipo de contrato...', false, [], 10) }}
				{{ Form::fhText('referencia', null, null, true, 4) }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhNumber('importe_contrato', 'Importe Contrato', false, 0, ['step' => 'any']) }}
				<hr />
				{{ Form::fhDate('fecha_firma', null, false, '-') }}
				{{ Form::fhDate('fecha_inicio_obras', 'Fecha Inicio Contrato', false, '-') }}
				{{ Form::fhDate('fecha_fin_obras', 'Fecha Fin Contrato', false, '-') }}
				<hr />
				{{ Form::fhSelect('responsable_contrato_id', $responsables, null, 'Responsable del Contrato', 'Seleccione el empleado...', true) }}
				{{ Form::fhSelect('tecnico_encargado_id', $responsables, null, 'Técnico Encargado', 'Seleccione el empleado...', false) }}
				{{ Form::fhSelect('tecnico_encargado2_id', $responsables, null, 'Técnico Encargado 2', 'Seleccione el empleado...', false) }}
				<hr />
				{{ Form::fhSelect('tecnico_prl_id', $tecnicos, null, 'Técnico P.R.L.', 'Seleccione el empleado...', true) }}
				{{ Form::fhSelect('coordinador_cap_id', $tecnicos, null, 'Coordinador C.A.P.', 'Seleccione el empleado...', false) }}
				{{ Form::fhSelect('tecnico_averias_id', $tecnicos, null, 'Técnico Averías', 'Seleccione el empleado...', false) }}
				<hr />
				{{ Form::fhTextarea('notas', 'Notas Públicas') }}
				<hr />
				{{ Form::fhTextarea('notas_privadas') }}

			{{ Form::fhClose('Guardar', 'contratos.index') }}
@endsection

@push('scripts')
<script src="{{ asset('/js/contratos.js') }}" type="text/javascript"></script>
<script type="text/javascript">
$(function() {
	$.Contratos().setupTipoContratoSelect();
});
</script>
@endpush
