@extends('layouts.app')

@section('htmlheader_title')
	Tipos de Máquinas
@endsection

@section('contentheader_title')
	Tipos de Máquinas
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Tipos de Máquinas</h3>
							<div class="box-tools"><a href="{{ route('tipos-maquinas.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a></div>
						</div>
						<div class="box-body">
							<table id="tipos-table">
								<thead>
									<tr>
										<th class="text-right">ID</th>
										<th>Nombre</th>
										<th>Notas</th>
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
			{ data: 'id', name: 'id', className: 'text-right', cellType: 'th', width: '10px' },
			{ data: 'nombre', name: 'nombre', cellType: 'th' },
			{ data: 'notas', name: 'notas' },
	];
	$.App.DT.set({
		tableName: 'tipos-table',
		columnsDef: columns,
		urlList: '{!! route('tipos-maquinas.rowsData') !!}',
		urlEdit: '{!! route('tipos-maquinas.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('tipo de máquina');
});
</script>
@endpush
