@extends('layouts.app')

@section('htmlheader_title')
	Nuevo tipo de documento
@endsection

@section('contentheader_title')
	Nuevo tipo de documento
@endsection

@push('styles')
	<link href="{{ asset('/plugins/selectize/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('main-content')
			{{ Form::fhOpen('tipos-documentos.store', 'Datos del nuevo tipo de documento') }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhText('referencia', null, null, false, 4) }}
				{{ Form::fhTextarea('notas') }}
				{{ Form::fhSelect('ambito', $doc_scopes, '', 'Ámbito', null, true) }}
				{{ Form::fhSelect('tipo_caducidad', $caducidades, 'N', 'Tipo Caducidad', null, true) }}
				{{ Form::fhText('tags', 'Palabras clave', 'Lista de palabras clave separadas por comas', false) }}
			{{ Form::fhClose('Guardar') }}
@endsection

@push('scripts')
<script>
$(function() {
	$('#ambito').select2({ minimumResultsForSearch: Infinity });
	$('#tipo_caducidad').select2({ minimumResultsForSearch: Infinity });
	$.App.Selectize.init();
});
</script>
@endpush
