@extends('layouts.app')

@section('htmlheader_title')
	Modificar datos del centro de trabajo
@endsection

@section('contentheader_title')
	Modificar datos del centro de trabajo
@endsection

@section('main-content')
	@include('documentos.modal-form')
@can('documentos.validar')
	@include('documentos.validation-form')
@endcan
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#t1" data-toggle="tab" >General</a></li>
							<li><a href="#t2" data-toggle="tab">Documentos</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="t1">
								{!! Form::model($model, ['route' => ['centros.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
									{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
									{{ Form::fhText('nombre', null, 'Nombre del centro de trabajo', true) }}
									<hr />
									{{ Form::fhText('direccion', 'Dirección', 'Dirección del centro de trabajo') }}
									{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
									{{ Form::fhText('municipio') }}
									{{ Form::fhSelect('provincia_id', $provincias, $model->provincia_id, 'Provincia', 'Seleccione la provincia...', true) }}
									<hr />
									{{ Form::fhText('telefono_centro', 'Teléfono del Centro', null, true, 4) }}
									{{ Form::fhText('fax_centro', 'Fax del Centro', null, false, 4) }}
									{{ Form::fhEmail('email_centro', 'Email del Centro', false) }}
									<hr />
									{{ Form::fhText('persona_contacto', 'Persona de Contacto', 'Nombre de la persona de contacto en el centro', false) }}
									{{ Form::fhText('telefono_contacto', 'Teléfono del Contacto', null, false, 4) }}
									{{ Form::fhEmail('email_contacto', 'Email del Contacto', false) }}
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Guardar cambios</button>
									</div>
								</form>
							</div>
							<div class="tab-pane" id="t2">
								<div id="documentos-box"></div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
@can('documentos.validar')
<script src="{{ asset('/js/documentos-validation.js') }}" type="text/javascript"></script>
@endcan
<script src="{{ asset('/js/documentos.js') }}" type="text/javascript"></script>
<script>
$(function() {
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan
	var doc = $.Documentos();
	doc.init('/centros');
	doc.createBox('documentos-box', 'Documentos del Centro', null, false);
	doc.setupDatatable('documentos-box-table');
});
</script>
@endpush
