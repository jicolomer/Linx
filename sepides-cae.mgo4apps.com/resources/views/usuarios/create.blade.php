@extends('layouts.app')

@section('htmlheader_title')
	Nuevo usuario
@endsection

@section('contentheader_title')
	Nuevo usuario
@endsection

@section('main-content')
			{{ Form::fhOpen('usuarios.store', 'Datos del nuevo usuario') }}
				{{ Form::fhText('nombre', null, null, true) }}
				{{ Form::fhEmail('email', null, true) }}
				{{ Form::fhText('telefono', 'Teléfono', null, false, 4) }}
				{{ Form::fhSelect('rol', $user_roles, null, null, 'Seleccione rol...', true) }}
				{{ Form::fhPass('password', 'Contraseña') }}
				{{ Form::fhPass('password_confirmation', 'Confirmar contraseña', false) }}
			{{ Form::fhClose('Guardar') }}
@endsection
