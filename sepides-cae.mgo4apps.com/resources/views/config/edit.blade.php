@extends('layouts.app')

@section('htmlheader_title')
	Configuración de la empresa
@endsection

@section('contentheader_title')
	Configuración de la empresa
@endsection

@push('styles')
	<link href="{{ asset('/plugins/selectize/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('main-content')
	{{ Form::fhOpen(['config.save', $model->id], '', $model, true) }}
		<h3>Empresa</h3>
		{{ Form::fhText('nombre_corto', null, null, true, 4) }}
		{{ Form::fhFile('logo', 'Logo Mediano') }}
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<img src="{{ asset(file_exists(public_path('img/logo-empresa.png')) ? 'img/logo-empresa.png' : 'img/logo-placeholder.png') }}" />
			</div>
		</div>
		{{ Form::fhFile('logo_small', 'Logo Pequeño') }}
		<div class="form-group">
			<div class="col-sm-10 col-sm-offset-2">
				<img src="{{ asset(file_exists(public_path('img/logo-empresa-small.png')) ? 'img/logo-empresa-small.png' : 'img/logo-placeholder-small.png') }}" />
			</div>
		</div>
		<hr />
		<h3>Documentos</h3>
		{{ Form::fhText('mimes_permitidos', 'Tipos Permitidos', 'Lista de tipos \'mime\' separados por comas', true) }}
		{{ Form::fhNumber('caducidad_m_dias', 'Aviso Caducidad Mensual', true, null, ['min' => 0, 'max' => 30]) }}
		{{ Form::fhNumber('caducidad_t_dias', 'Aviso Caducidad Trimestral', true, null, ['min' => 0, 'max' => 90]) }}
		{{ Form::fhNumber('caducidad_s_dias', 'Aviso Caducidad Semestral', true, null, ['min' => 0, 'max' => 180]) }}
		{{ Form::fhNumber('caducidad_a_dias', 'Aviso Caducidad Anual', true, null, ['min' => 0, 'max' => 365]) }}
		{{ Form::fhNumber('caducidad_v_dias', 'Aviso Caducidad a Vencimiento', true, null, ['min' => 0, 'max' => 365]) }}
		<hr />
		<h3>Contratos</h3>
		{{ Form::fhCheck('invitar_subcontratistas', '¿Invitar a los subcontratistas?', true, $model->invitar_subcontratistas) }}
		<hr />
		<h3>Tablas</h3>
		{{ Form::fhSelect('filas_tablas', $filas_tablas, $model->filas_tablas, null, null, true) }}
		{{ Form::fhSelect('filas_tablas_modal', $filas_tablas, $model->filas_tablas_modal, 'Filas Tablas Modales', null, true) }}

	{{ Form::fhClose('Guardar cambios') }}
@endsection

@push('scripts')
<script>
$(function() {
	$.App.Selectize.init('{{ $model->mimes_permitidos }}', '#mimes_permitidos');
	$('#filas_tablas').select2({ minimumResultsForSearch: Infinity });
	$('#filas_tablas_modal').select2({ minimumResultsForSearch: Infinity });
});
</script>
@endpush
