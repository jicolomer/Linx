@extends('layouts.app')

@section('htmlheader_title')
	Contactar con Soporte
@endsection

@section('contentheader_title')
	Contactar con Soporte
@endsection

@section('main-content')
	<link href="{{ asset('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.min.css') }}" rel="stylesheet" type="text/css">
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="box box-{{ isset($respuesta_soporte) ? 'danger' : 'primary' }}">
				<form method="POST" action="{!! route('soporte.enviar') !!}" class="form-horizontal">
					{{ csrf_field() }}
				@if (isset($respuesta_soporte))
					<input type="hidden" name="respuesta_soporte" value="true" />
				@endif
					<div class="box-header">
						<h3 class="box-title">{{ isset($respuesta_soporte) ? 'Responder a usuario' : 'Enviar mensaje a soporte' }}</h3>
					</div>
					<div class="box-body">
					@if (isset($respuesta_soporte))
						{{ Form::fhText('usuario_nombre', 'Nombre del usuario', null, true, 10, $usuario_nombre) }}
						{{ Form::fhEmail('email_respuesta', 'Email de respuesta', true, $email_respuesta) }}
						{{ Form::fhText('asunto', null, 'Indique el asunto del mensaje', true, 10, $asunto) }}
					@else
						{{ Form::fhEmail('email_respuesta', 'Email de respuesta', true, Auth::user()->email, ['readonly' => true]) }}
						{{ Form::fhText('asunto', null, 'Indique el asunto del mensaje', true) }}
					@endif
						<hr />
						{{ Form::fhTextarea('mensaje', null, isset($respuesta_soporte) ? 'Mensaje para el usuario...' : 'Mensaje que quiere enviar a soporte...', true) }}
					</div>
					<div class="box-footer">
					@if (isset($respuesta_soporte))
						<button type="submit" class="btn btn-danger"><i class="fa fa-paper-plane"></i> &nbsp;Responder</button>
					@else
						<button type="submit" class="btn btn-primary"><i class="fa fa-paper-plane"></i> &nbsp;Enviar</button>
					@endif
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script src="{{ asset('/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js') }}"></script>
<script src="{{ asset('/plugins/bootstrap-wysihtml5/locales/bootstrap-wysihtml5.es-ES.js') }}"></script>
<script>
$(function() {
	$('#mensaje').wysihtml5({
		locale: "es-ES",
		toolbar: {
    		"font-styles": false,
    		"link": false,
    		"image": false,
    		"blockquote": false,
			'fa': true,
		}
	});
});
</script>
@endpush
