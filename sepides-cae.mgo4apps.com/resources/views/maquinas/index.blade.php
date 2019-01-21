@extends('layouts.app')

@section('htmlheader_title')
	Máquinas
@endsection

@section('contentheader_title')
	Máquinas
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Máquinas</h3>
							<div class="box-tools">
								<a href="{{ route('maquinas.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva</a>
								&nbsp;
								<button class="btn archive-button"><i class="fa fa-archive"></i> Archivados</button>
							</div>
						</div>
						<div class="box-body">
							<table id="maquinas-table">
								<thead>
									<tr>
										<th class="text-center">ID</th>
									@if (! Auth::user()->isExterno())
										<th data-priority="3">Empresa</th>
									@endif
										<th data-priority="4">Tipo</th>
										<th data-priority="1">Nombre</th>
										<th data-priority="2">Matrícula</th>
										<th>Marca</th>
										<th>Modelo</th>
										<th>Documentación</th>
									</tr>
								</thead>
								<tbody></tbody>
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
		{ data: 'id', name: 'maquinas.id', className: 'text-right', cellType: 'th', width: '10px' },
	@if (! Auth::user()->isExterno())
		{ data: 'empresa', name: 'empresa', cellType: 'th' },
	@endif
		{ data: 'tipo', name: 'tipo', cellType: 'th' },
		{ data: 'nombre', name: 'maquinas.nombre', cellType: 'th' },
		{ data: 'matricula', name: 'matricula', cellType: 'th' },
		{ data: 'marca', name: 'marca' },
		{ data: 'modelo', name: 'modelo' },
		{ data: 'documentacion', name: 'documentacion', orderable: false, searchable: false, width: '10px', className: 'text-center' },
	];
	$.App.DT.set({
		tableName: 'maquinas-table',
		columnsDef: columns,
		urlList: '{!! route('maquinas.rowsData') !!}',
		urlEdit: '{!! route('maquinas.edit', 'XX') !!}',
		addActionColumn: false,
	});
});
</script>
@endpush
