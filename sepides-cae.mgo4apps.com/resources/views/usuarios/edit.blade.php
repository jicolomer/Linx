@extends('layouts.app')

@section('htmlheader_title')
	Usuario: {{ $model->nombre . ' (#' . $model->id . ')' }}
@endsection

@section('contentheader_title')
	Usuario: {{ $model->nombre . ' (#' . $model->id . ')' }}
@endsection

@section('main-content')
			{{ Form::fhOpen(['usuarios.update', $model->id], 'Datos del usuario', $model) }}
				{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
				{{ Form::fhText('nombre', null, null, true, 10, null, ($is_trabajador == true) ? ['readonly' => 'true'] : []) }}
				<hr />
				{{ Form::fhEmail('email', null, true, null, ($is_trabajador == true) ? ['readonly' => 'true'] : []) }}
				{{ Form::fhText('telefono', 'Teléfono', null, false, 4, null, ($is_trabajador == true) ? ['readonly' => 'true'] : []) }}
				<hr />
				{{ Form::hidden('empresa_id', $model->empresa_id) }}
				{{ Form::fhText('empresa_nombre', 'Empresa', null, true, 4, $empresa_nombre, ['readonly' => 'true']) }}
				{{ Form::fhSelect('rol', $user_roles, $role_id, null, 'Seleccione rol...', true, ($is_trabajador == true) ? ['disabled' => 'true'] : []) }}
				</div>
				<div class="box-footer">
				@if($is_trabajador == false)
					<button type="submit" class="btn btn-primary">Guardar cambios</button>
				@else
					<a href="{!! route('trabajadores.edit', $model->trabajador->id) !!}" class= "btn btn-success"><i class="fa fa-pencil"></i> Editar trabajador</a>
				@endif
					<a href="{!! route('usuarios.block', $model->id) !!}"
						class= "ask-for-confirmation"
						data-msg="Se va a bloquear el acceso del usuario a la aplicación..."
						data-loading="Bloqueando usuario...">
						<i class="fa fa-ban text-orange"></i> Bloquear acceso
					</a>
					<a href="{!! route('usuarios.resetPassword', $model->id) !!}"
						class= "ask-for-confirmation"
						data-msg="Se va a resetear la contraseña del usuario..."
						data-loading="Enviando email...">
						<i class="fa fa-refresh text-danger"></i> Resetear contraseña
					</a>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
