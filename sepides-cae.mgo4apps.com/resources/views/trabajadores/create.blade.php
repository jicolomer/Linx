@extends('layouts.app')

@section('htmlheader_title')
	Nuevo trabajador
@endsection

@section('contentheader_title')
	Nuevo trabajador
@endsection

@section('main-content')
	{{ Form::fhOpen('trabajadores.store', 'Datos del nuevo trabajador') }}
	@if($empresa_id === 0 || $empresa_id > 0)
		{{ Form::hidden('empresa_id', $empresa_id) }}
		{{ Form::fhText('empresa_nombre', 'Empresa', null, true, 4, $empresa_nombre, ['readonly' => 'true']) }}
	@else
		{{ Form::fhSelect('empresa_id', $empresas, null, 'Empresa', 'Empresa del trabajador...', true) }}
	@endif
		{{ Form::fhText('nombre', null, null, true, 4) }}
		{{ Form::fhText('apellidos', null, null, true) }}
		{{ Form::fhText('nif', 'NIF/DNI', null, true, 4) }}
		{{ Form::fhText('nss', 'Nº Seg. Social', null, false, 4) }}
		{{ Form::fhDate('fecha_nacimiento', null, false, '01/01/1980') }}
		<hr />
		{{ Form::fhText('direccion', 'Dirección') }}
		{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
		{{ Form::fhText('municipio') }}
		{{ Form::fhSelect('provincia_id', $provincias, null, 'Provincia', 'Seleccione la provincia...', false) }}
		<hr />
		{{ Form::fhText('telefono', 'Teléfono', null, false, 4) }}
		{{ Form::fhText('telefono2', 'Teléfono 2', null, false, 4) }}
		{{ Form::fhEmail('email') }}
		<hr />
		{{ Form::fhText('puesto', 'Puesto de trabajo', null, true) }}
		{{ Form::fhCheck('recurso_preventivo', '¿Recurso Preventivo?') }}
		{{ Form::fhCheck('delegado_prevencion', '¿Delegado de Prevención?') }}
		{{ Form::fhDate('fecha_alta', null, true) }}
		<hr />
		{{ Form::fhCheck('crear_usuario', 'Crear nuevo usuario') }}
		{{ Form::fhSelect('user_rol', $user_roles, null, 'Rol de usuario:', 'Seleccione un rol de usuario...', true) }}
	{{ Form::fhClose('Guardar') }}
@endsection

@push('scripts')
<script>
$(function() {

	var checked = false;
	var $user = $('#crear_usuario');
	var $roles = $('#user_rol-group');

	function getCheckValue() {
		checked = $user.iCheck('update')[0].checked;
	}

	function toggleUserRoles() {
		getCheckValue();
		if (checked) {
			@if($empresa_id == null || $empresa_id > 0)
				$('#user_rol').val({{ App\User::findRoleBySlug('externo')->id }}).trigger('change');
			@endif
			$roles.show();
		} else {
			$roles.hide();
		}
	}

	$user.on('ifToggled', function(e) {
		toggleUserRoles();
	});

	toggleUserRoles();

});
</script>
@endpush
