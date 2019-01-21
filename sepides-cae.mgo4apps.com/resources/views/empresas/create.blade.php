@extends('layouts.app')

@section('htmlheader_title')
	Nueva empresa
@endsection

@section('contentheader_title')
	Nueva empresa
@endsection

@section('main-content')
			{{ Form::fhOpen('empresas.store', 'Datos de la nueva empresa') }}
				{{ Form::fhText('razon_social', null, 'Razón social o nombre del autónomo', true) }}
				{{ Form::fhText('cif', 'CIF/DNI', null, true, 4) }}
				<hr />
				{{ Form::fhText('direccion', 'Dirección') }}
				{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
				{{ Form::fhText('municipio') }}
				{{ Form::fhSelect('provincia_id', $provincias, null, 'Provincia', 'Seleccione la provincia...') }}
				<hr />
				{{ Form::fhText('telefono', 'Teléfono', null, false, 4) }}
				{{ Form::fhText('telefono2', 'Teléfono 2', null, false, 4) }}
				{{ Form::fhText('fax', null, null, false, 4) }}
				<hr />
				<div class="form-group{{ $errors->has('codigo_cnae') ? ' has-error' : '' }}">
					{{ Form::label('codigo_cnae', 'Código CNAE', ['class' => 'control-label col-sm-2']) }}
					<div class="col-md-2">
						<input type='text' id="codigo_cnae_c" name="codigo_cnae_c" class="form-control pull-left text-center" disabled>
					</div>
					<div class="col-md-8">
						{{ Form::select('codigo_cnae', $cnaes, null, ['placeholder' => 'Seleccione el código CNAE de la empresa...', 'class' => 'form-control select2']) }}
					</div>
				</div>
				{{ Form::fhSelect('modalidad_preventiva', $modalidades, 'SPA', null, 'Seleccione modalidad...', true) }}
				<hr />
				{{ Form::fhCheck('construccion', '¿Se dedica a la construccion?') }}
				{{ Form::fhSelect('actividad_construccion', $actividades_construccion, null, 'Actividad de construccion', 'Seleccione actividad...', true) }}
				{{ Form::fhCheck('plantilla_indefinida', '¿Tiene += 30% trabajadores indefinidos?') }}
				{{ Form::fhText('rea', 'Inscripción R.E.A.', null, false, 4) }}
				<hr />
				{{ Form::fhCheck('autonomo', '¿Es Autónomo?') }}
				{{ Form::fhCheck('trabajadores_a_cargo', '¿Tiene trabajadores a su cargo?') }}

			{{ Form::fhClose('Guardar') }}
@endsection

@push('scripts')
<script src="{{ asset('/js/empresas.view.js') }}" type="text/javascript"></script>
@endpush
