@extends('layouts.app')

@section('htmlheader_title')
	Control de Accesos
@endsection

@section('contentheader_title')
	Control de Accessos
@endsection

@section('main-content')
	<div class="row">
		<div class="col-md-10 col-md-offset-1">
			<div class="box box-primary">
				<form method="POST" action="{!! route('compruebaAcceso') !!}" class="form-horizontal">
					{{ csrf_field() }}
					<div class="box-header">
						<h3 class="box-title">Control de Accesos</h3>
					</div>
					<div class="box-body">
						<div id='callout_centro' class="callout callout-warning" style="display:none">
							<h4>¡Seleccione un <strong>Centro de Trabajo</strong>!</h4>
							<p>Por favor seleccione un <strong>Centro de Trabajo</strong> antes de comprobar los permisos de acceso.</p>
						</div>
						<div id="centro_id_text"{!! $centro_id == null ? ' style="display:none"' : '' !!}>
							<input type="hidden" id="centro_id" name="centro_id" value={{ $centro_id }}>
							{{ Form::fhText('centro_nombre', 'Centro de Trabajo', null, true, 10, $centro_nombre, ['readonly' => 'readonly']) }}
						</div>
						<div id="centro_id_select"{!! $centro_id != null ? ' style="display:none"' : '' !!}>
							{{ Form::fhSelect('new_centro_id', $centros, null, 'Centro de Trabajo', 'Seleccione el centro de trabajo...', true, [], 10) }}
						</div>
						<hr />
						<div id="nif_group" class="form-group{{ $errors->has('nif') ? ' has-error' : '' }}">
							{{ Form::label('nif', 'NIF/DNI', ['class' => 'control-label col-sm-2']) }}
							<div class="col-md-4">
								{{ Form::text('nif', '', ['class' => 'form-control']) }}
							</div>
						</div>
						<hr />
						<div id="matricula_group" class="form-group{{ $errors->has('matricula') ? ' has-error' : '' }}">
							{{ Form::label('matricula', 'Matrícula', ['class' => 'control-label col-sm-2']) }}
							<div class="col-md-4">
								{{ Form::text('matricula', '', ['class' => 'form-control']) }}
							</div>
						</div>
					</div>
					<div class="box-footer">
						<button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> &nbsp; Comprobar</button>
					@if ($centro_id != null)
						<button id="cambiarCentroBtn" type="button" class="btn btn-default">Cambiar Centro</button>
					@endif
					</div>
				</form>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script>
$(function() {
	var $centro_id = $('#centro_id');
	var $new_centro_id = $('#new_centro_id');
	var $callout_centro = $('#callout_centro');
@if ($centro_id == null)
	$callout_centro.show();
@else
	$('#cambiarCentroBtn').on('click', function() {
		$('#cambiarCentroBtn').hide();
		$('#centro_id_text').hide();
		$('#centro_id_select').show();
		$centro_id.val('');
		$new_centro_id.select2('open');
	});
@endif
	$new_centro_id.on('select2:select', function(e) {
		$callout_centro.fadeOut();
	});
	$new_centro_id.on('select2:unselect', function(e) {
		$callout_centro.fadeIn();
	});
	$('#nif').on('input', function() {
		var val = $(this).val().trim();
		if (val.length > 0) {
			$('#matricula').val('');
		}
	});
	$('#matricula').on('input', function() {
		var val = $(this).val().trim();
		if (val.length > 0) {
			$('#nif').val('');
		}
	});
});
</script>
@endpush
