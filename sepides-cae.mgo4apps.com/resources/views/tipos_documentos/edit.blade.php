@extends('layouts.app')

@section('htmlheader_title')
	Modificar tipo de documento
@endsection

@section('contentheader_title')
	Modificar tipo de documento: {{ $model->nombre }}
@endsection

@push('styles')
	<link href="{{ asset('/plugins/selectize/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('main-content')
			{{ Form::fhOpen(['tipos-documentos.update', $model->id], 'Datos del tipo de documento', $model) }}
				{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhText('referencia', null, null, false, 4) }}
				{{ Form::fhTextarea('notas') }}
				{{ Form::fhSelect('ambito', $doc_scopes, $model->ambito, 'Ámbito', null, true) }}
				{{ Form::fhSelect('tipo_caducidad', $caducidades, $model->tipo_caducidad, 'Tipo Caducidad', null, true) }}
				{{ Form::fhText('tags', 'Palabras clave', 'Lista de palabras clave separadas por comas', false, 10, $tags? $tags : ' ') }}
			{{ Form::fhClose('Guardar cambios') }}
@endsection

@push('scripts')
<script>
$(function() {
	$('#ambito').select2({ minimumResultsForSearch: Infinity });
	$('#tipo_caducidad').select2({ minimumResultsForSearch: Infinity });
	$.App.Selectize.init('{{ $tags }}');
});
</script>
@endpush
