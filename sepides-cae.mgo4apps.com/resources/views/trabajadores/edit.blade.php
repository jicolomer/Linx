@extends('layouts.app')

@section('htmlheader_title')
	Trabajador: {{ $model->nombreCompleto(true) }}
@endsection

@section('contentheader_title')
	Trabajador: {{ $model->nombreCompleto(true) }}
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
						@if($model->empresa_id > 0)
							<li><a href="#t2" data-toggle="tab">Formación</a></li>
							<li><a href="#t3" data-toggle="tab">Información</a></li>
							<li><a href="#t4" data-toggle="tab">Registros de EPIS</a></li>
							<li><a href="#t5" data-toggle="tab">Vigilancia Salud</a></li>
							<li><a href="#t6" data-toggle="tab">Otros</a></li>
						@endif
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="t1">
								{!! Form::model($model, ['route' => ['trabajadores.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
									{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
									{{ Form::hidden('empresa_id', $model->empresa_id) }}
									{{ Form::fhText('empresa_nombre', 'Empresa', null, true, 4, $empresa_nombre, ['readonly' => 'true']) }}
									{{ Form::fhText('nombre', null, null, true, 4) }}
									{{ Form::fhText('apellidos', null, null, true) }}
									{{ Form::fhText('nif', 'NIF/DNI', null, true, 4) }}
									{{ Form::fhText('nss', 'Nº Seg. Social', null, false, 4) }}
									{{ Form::fhDate('fecha_nacimiento', null, false, ($model->fecha_nacimiento == null) ? '-' : $model->fecha_nacimiento) }}
									<hr />
									{{ Form::fhText('direccion', 'Dirección') }}
									{{ Form::fhText('codigo_postal', 'Código Postal', null, false, 4) }}
									{{ Form::fhText('municipio') }}
									{{ Form::fhSelect('provincia_id', $provincias, $model->provincia_id, 'Provincia', 'Seleccione la provincia...', false) }}
									<hr />
									{{ Form::fhText('telefono', 'Teléfono', null, false, 4) }}
									{{ Form::fhText('telefono2', 'Teléfono 2', null, false, 4) }}
									{{ Form::fhEmail('email', null, ($model->user_id != null)) }}
									<hr />
									{{ Form::fhText('puesto', null, 'Puesto de trabajo', true) }}
									{{ Form::fhCheck('recurso_preventivo', '¿Recurso Preventivo?', false, $model->recurso_preventivo) }}
									{{ Form::fhCheck('delegado_prevencion', '¿Delegado de Prevención?', false, $model->delegado_prevencion) }}
									{{ Form::fhDate('fecha_alta', null, true, $model->fecha_alta) }}
									<hr />
								@if ($model->user_id == null)
									{{ Form::fhCheck('crear_usuario', 'Crear nuevo usuario') }}
								@else
									<div class="form-group" id="usuario-group">
									    {{ Form::label('usuario', 'Usuario:', ['class' => 'control-label col-sm-2']) }}
										{{ Form::hidden('usuario_id') }}
									    <div class="col-sm-4">
									        {{ Form::text('usuario', $model->user->nombre . ' (#' . $model->user_id . ')', ['class' => 'form-control', 'readonly' => 'true']) }}
									    </div>
										<div class="col-sm-4">
											<a href="{!! route('usuarios.block', $model->id) !!}"
												class= "ask-for-confirmation"
												data-msg="Se va a bloquear el acceso del trabajador a la aplicación..."
												data-loading="Bloqueando usuario...">
												<i class="fa fa-ban text-orange"></i> Bloquear acceso
											</a>
											<a href="{!! route('usuarios.resetPassword', $model->id) !!}"
												class= "ask-for-confirmation"
												data-msg="Se va a resetear la contraseña del trabajador..."
												data-loading="Enviando email...">
												<i class="fa fa-refresh text-danger"></i> Resetear contraseña
											</a>
										</div>
									</div>
								@endif
									{{ Form::fhSelect('user_rol', $user_roles, ($model->user == null ? null : $model->user->roles()->first()->id), 'Rol de usuario:', 'Seleccione un rol de usuario...', true) }}
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Guardar cambios</button>
										<button id="delete_button" type="button" class="btn btn-danger pull-right"><i class="fa fa-times"></i> Dar de baja</button>
									</div>
								</form>
							</div>
						@if($model->empresa_id > 0)
							<div class="tab-pane" id="t2">
								<div class="box-header">
									<h3 class="box-title">Documentos de Formación</h3>
									<div class="box-tools">
										<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#documentos-modal-dialog" data-title="Documento de Formación" data-tipo="FOR" data-new="true"><i class="fa fa-plus"></i> Añadir</button>
									</div>
								</div>
								<div class="box-body">
									<table id="doc-formacion-table">
										<thead>
											<tr>
												<th class="text-right">ID</th>
												<th>Documento</th>
												<th>Horas For.</th>
												<th>Fecha Doc.</th>
												<th>Estatus Caducidad</th>
												<th>¿Validado?</th>
												<th>Notas</th>
												<th>Palabras clave</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>
							<div class="tab-pane" id="t3">
								<div id="doc-informacion-box"></div>
							</div>
							<div class="tab-pane" id="t4">
								<div id="doc-epis-box"></div>
							</div>
							<div class="tab-pane" id="t5">
								<div id="doc-vigilancia-box"></div>
							</div>
							<div class="tab-pane" id="t6">
								<div id="doc-otros-box"></div>
							</div>
						@endif
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
	var checked = false;
	var $create_user_check = $('#crear_usuario');
	var $create_user_group = $('#crear_usuario-group');
	var $roles_group = $('#user_rol-group');
	function toggleUserRolesGroup(value) {
		if (value) {
			$roles_group.show();
		} else {
			$roles_group.hide();
		}
	}
@if ($model->user_id != null)
	toggleUserRolesGroup(true);
@else
	function getCheckValue() {
		checked = $create_user_check.iCheck('update')[0].checked;
	}
	function toggleUserRoles() {
		getCheckValue();
		toggleUserRolesGroup(checked);
	@if($model->empresa_id > 0)
		if (checked) {
			$('#user_rol').val({{ App\User::findRoleBySlug('externo')->id }}).trigger('change');
		}
	@endif
	}
	$create_user_check.on('ifToggled', function(e) {
		toggleUserRoles();
	});
	toggleUserRoles();
@endif
@if($model->empresa_id > 0)
	var dtFor = $.Documentos();
	dtFor.init('/trabajadores');
	var columns_formacion = [
		{ data: 'id', name: 'id', className: 'text-right', cellType: 'th' },
		{ data: 'nombre', name: 'nombre', cellType: 'th' },
		{ data: 'horas_formacion', name: 'horas_formacion', className: 'text-right' },
		{ data: 'fecha_documento', name: 'fecha_documento', className: 'text-center' },
		{ data: 'status_caducidad', name: 'status_caducidad', className: 'text-center vcenter', orderable: false, searchable: false },
		{ data: 'status_validacion', name: 'status_validacion', className: 'text-center vcenter', orderable: false, searchable: false },
		{ data: 'notas', name: 'notas' },
		{ data: 'tags', name: 'tags' },
	];
	dtFor.setupDatatable('doc-formacion-table', '{!! route('trabajadores.documentosData') !!}?tdt=FOR', columns_formacion);
	var dtInf = $.Documentos();
	dtInf.setBaseRoute('/trabajadores');
	dtInf.createBox('doc-informacion-box', 'Documentos de Información', null, false, { 'data-title': 'Documento de Información', 'data-tipo': 'INF' });
	dtInf.setupDatatable('doc-informacion-box-table', '{!! route('trabajadores.documentosData') !!}?tdt=INF');
	var dtEpi = $.Documentos();
	dtEpi.setBaseRoute('/trabajadores');
	dtEpi.createBox('doc-epis-box', 'Registros de EPIS', null, false, { 'data-title': 'Registro de EPIS', 'data-tipo': 'EPI' });
	dtEpi.setupDatatable('doc-epis-box-table', '{!! route('trabajadores.documentosData') !!}?tdt=EPI');
	var dtVis = $.Documentos();
	dtVis.setBaseRoute('/trabajadores');
	dtVis.createBox('doc-vigilancia-box', 'Documentos de Vigilancia de la Salud', null, false, { 'data-title': 'Documento de Vigilancia de la Salud', 'data-tipo': 'VIS' });
	dtVis.setupDatatable('doc-vigilancia-box-table', '{!! route('trabajadores.documentosData') !!}?tdt=VIS');
	var dtOtr = $.Documentos();
	dtOtr.setBaseRoute('/trabajadores');
	dtOtr.createBox('doc-otros-box', 'Otros documentos', null, false, { 'data-title': 'Otro tipo de documento', 'data-tipo': 'OTR' });
	dtOtr.setupDatatable('doc-otros-box-table', '{!! route('trabajadores.documentosData') !!}?tdt=OTR');
@can('documentos.validar')
	$.DocumentosValidation().init();
@endcan
@endif
	$('#delete_button').on('click', function(e) {
		var msg = '¿Está seguro de querer <strong>dar de baja</strong> al trabajador <strong>\'{{ $model->nombreCompleto() }}\'</strong>?<br /><br />Se archivarán también sus <strong>Documentos</strong>';
		msg += '{!! ($model->user_id == null) ? '.' : ' y su <strong>Usuario</strong> (ya no podrá acceder).' !!}';
		msg += '<br /><br />Además, si está incluído en algún <strong>Contrato</strong>, perderá el acceso a los <strong>Centros de trabajo</strong> para los que tuviera permiso.'
		bootbox.dialog({
			title: "Por favor, confirme",
			message: msg,
			className: "modal-danger",
			onEscape: function() {},
			buttons: {
				si: {
					label: "Sí, quiero darlo de baja",
					className: "btn-outline pull-right",
					callback: function() {
						var route = "{{ route('trabajadores.remove', $model->id) }}";
						$.post(route, function(d) {
							if (d != null) {
								if (d.result != null) {
									if (d.result == 'success') {
										location = "{{ route('trabajadores.index') }}";
										return true;
									}
								}
							}
							bootbox.alert("Ha ocurrido un error y no se ha podido dar de baja el trabajador.")
						});
					}
				},
				no: {
					label: "Cancelar",
					className: "btn-outline pull-left",
				},
			}
		});
	});
})
</script>
@endpush
