@extends('layouts.app')

@section('htmlheader_title')
	Nuevo tipo de máquina
@endsection

@section('contentheader_title')
	Nuevo tipo de máquina
@endsection

@section('main-content')
			{{ Form::fhOpen('tipos-maquinas.store', 'Datos del nuevo tipo de máquina') }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhTextarea('notas') }}
			{{ Form::fhClose('Guardar') }}
@endsection
