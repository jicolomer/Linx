@extends('layouts.app')

@section('htmlheader_title')
	Nuevo centro de trabajo
@endsection

@section('contentheader_title')
	Nuevo centro de trabajo
@endsection

@section('main-content')
			{{ Form::fhOpen('centros.store', 'Datos del nuevo centro') }}
				{{ Form::fhText('nombre', null, 'Nombre del centro de trabajo', true) }}
				<hr />
				{{ Form::fhText('direccion', 'Dirección', 'Dirección del centro de trabajo') }}
				{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
				{{ Form::fhText('municipio') }}
				{{ Form::fhSelect('provincia_id', $provincias, null, 'Provincia', 'Seleccione la provincia...', true) }}
				<hr />
				{{ Form::fhText('telefono_centro', 'Teléfono del Centro', null, true, 4) }}
				{{ Form::fhText('fax_centro', 'Fax del Centro', null, false, 4) }}
				{{ Form::fhEmail('email_centro', 'Email del Centro', false) }}
				<hr />
				{{ Form::fhText('persona_contacto', 'Persona de Contacto', 'Nombre de la persona de contacto en el centro', false) }}
				{{ Form::fhText('telefono_contacto', 'Teléfono del Contacto', null, false, 4) }}
				{{ Form::fhEmail('email_contacto', 'Email del Contacto', false) }}
			{{ Form::fhClose('Guardar') }}
@endsection
