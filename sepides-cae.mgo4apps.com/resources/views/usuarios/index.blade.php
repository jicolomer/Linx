@extends('layouts.app')

@section('htmlheader_title')
	Usuarios
@endsection

@section('contentheader_title')
	Usuarios
@endsection

@section('main-content')
			<div class="modal modal-danger fade" id="delete_modal" tabindex="-1" role="dialog">
				<div class="modal-dialog" role="document">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<h4 class="modal-title">Por favor confirme...</h4>
						</div>
						<div class="modal-body">
							<h4>¿Está seguro de querer eliminar el usuario?</h4>
							<p><em>(Se almacenará en el Histórico, pero ya no podrá entrar al sistema)</em></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-outline pull-left" data-dismiss="modal">Cancelar</button>
							<button type="button" class="btn btn-outline">Eliminar usuario</button>
						</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-xs-12">
					<div class="box">
						<div class="box-header">
							<h3 class="box-title">Listado de usuarios</h3>
							<div class="box-tools">
								<a href="{{ route('usuarios.create') }}" class="btn btn-primary">
									<i class="fa fa-plus"></i> Nuevo
								</a>
								&nbsp;
								<button class="btn archive-button"><i class="fa fa-archive"></i> Archivados</button>
							</div>
						</div>
						<div class="box-body">
							<table id="users-table">
								<thead>
									<tr>
										<th class="text-right">ID</th>
										<th>Nombre</th>
										<th>Email</th>
										<th>Teléfono</th>
										<th>Rol</th>
										<th>Fecha Alta</th>
										<th data-priority="1"></th>
									</tr>
								</thead>
							</table>
						</div>
					</div>
				</div>
			</div>
@endsection

@push('scripts')
<script>
$(function() {
	var columns = [
		{ data: 'id', name: 'id', className: 'text-right', cellType: 'th' },
		{ data: 'nombre', name: 'nombre', cellType: 'th' },
		{ data: 'email', name: 'email' },
		{ data: 'telefono', name: 'telefono' },
		{ data: 'rol', name: 'rol' },
		{ data: 'created_at', name: 'created_at' },
	];
	$.App.DT.set({
		tableName: 'users-table',
		columnsDef: columns,
		urlList: '{!! route('usuarios.rowsData') !!}',
		urlEdit: '{!! route('usuarios.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('usuario', '(No se eliminará defininivamente, sino que se almacenará en el <em>Archivo</em> y ya no podrá acceder al sistema)');
});
</script>
@endpush
