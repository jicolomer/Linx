@extends('layouts.app')

@section('htmlheader_title')
	Tipos de Contratos
@endsection

@section('contentheader_title')
	Tipos de Contratos
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Tipos de Contratos</h3>
							<div class="box-tools">
								<a href="{{ route('tipos-contratos.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
								&nbsp;
								<button class="btn archive-button"><i class="fa fa-archive"></i> Archivados</button>
							</div>
						</div>
						<div class="box-body">
							<table id="tipos-table">
								<thead>
									<tr>
										<th class="text-right">ID</th>
										<th>Nombre</th>
										<th>Notas</th>
										<th class='text-center'>¿Vigilar nivel subcontratas?</th>
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
		{ data: 'nivelSubcontratas', name: 'nivelSubcontratas', orderable: false, searchable: false, className: 'text-center', width: '30px' },
	];
	$.App.DT.set({
		tableName: 'tipos-table',
		columnsDef: columns,
		urlList: '{!! route('tipos-contratos.rowsData') !!}',
		urlEdit: '{!! route('tipos-contratos.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('tipo de contrato');
});
</script>
@endpush
