@extends('layouts.app')

@section('htmlheader_title')
	Nuevo tipo de contrato
@endsection

@section('contentheader_title')
	Nuevo tipo de contrato
@endsection

@section('main-content')
			{{ Form::fhOpen('tipos-contratos.store', 'Datos del nuevo tipo de contrato') }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhTextarea('notas') }}
				{{ Form::fhCheck('nivel_subcontratas', '¿Vigilar nivel de subcontratas?', false, false) }}
			{{ Form::fhClose('Guardar') }}
@endsection
