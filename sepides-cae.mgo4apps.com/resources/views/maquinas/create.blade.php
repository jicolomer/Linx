@extends('layouts.app')

@section('htmlheader_title')
	Nueva máquina
@endsection

@section('contentheader_title')
	Nueva máquina
@endsection

@section('main-content')
	{{ Form::fhOpen('maquinas.store', 'Datos de la nueva máquina') }}
	@if($empresa_id === 0 || $empresa_id > 0)
		{{ Form::hidden('empresa_id', $empresa_id) }}
		{{ Form::fhText('empresa_nombre', 'Empresa', null, true, 4, $empresa_nombre, ['readonly' => 'true']) }}
	@else
		{{ Form::fhSelect('empresa_id', $empresas, null, 'Empresa', 'Empresa...', true) }}
	@endif
		<hr />
		{{ Form::fhSelect('tipo_maquina_id', $tipos_maquinas, null, 'Tipo de máquina', 'Tipo de máquina...', true) }}
		{{ Form::fhText('nombre', null, null, true) }}
		{{ Form::fhText('marca') }}
		{{ Form::fhText('modelo') }}
		<hr />
		{{ Form::fhText('matricula', 'Matrícula', null, true, 4) }}
		{{ Form::fhText('num_serie', 'Número de serie', null, false, 4) }}
		{{ Form::fhText('num_bastidor', 'Número de bastidor', null, false, 4) }}
		<hr />
		<div id="anio_fabricacion_group" class="required form-group{{ $errors->has('anio_fabricacion') ? ' has-error' : '' }}">
			{{ Form::label('anio_fabricacion', 'Año de fabricación', ['class' => 'control-label col-sm-2']) }}
			<div class="col-md-4">
				{{ Form::number('anio_fabricacion', Jenssegers\Date\Date::now()->format('Y'), ['class' => 'form-control']) }}
			</div>
			<div class="col-md-2">
				<span id="anio_fabricacion_text" class="badge bg-orange" style="margin-top:8px"></span>
			</div>
		</div>
		<hr />
		{{ Form::fhTextarea('notas') }}
		{{ Form::fhDate('fecha_alta', null, true) }}
	{{ Form::fhClose('Guardar') }}
@endsection

@push('scripts')
<script src="{{ asset('/js/maquinas.view.js') }}" type="text/javascript"></script>
@endpush
