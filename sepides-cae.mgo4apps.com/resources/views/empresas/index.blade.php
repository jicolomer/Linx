@extends('layouts.app')

@section('htmlheader_title')
	Empresas
@endsection

@section('contentheader_title')
	Empresas
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Empresas</h3>
							<div class="box-tools"><a href="{{ route('empresas.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nueva</a></div>
						</div>
						<div class="box-body">
							<table id="empresas-table">
								<thead>
									<tr>
										<th class="text-center">ID</th>
										<th data-priority="2">Razón Social</th>
										<th>CIF/DNI</th>
										<th>Municipio</th>
										<th>Provincia</th>
										<th>Teléfono</th>
										<th>Mod. Preventiva</th>
										<th>¿Construcción?</th>
										<th>¿Autónomo?</th>
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
	columns = [
		{ data: 'id', name: 'id', className: 'text-right', cellType: 'th' },
		{ data: 'razon_social', name: 'razon_social', cellType: 'th' },
		{ data: 'cif', name: 'cif' },
		{ data: 'municipio', name: 'municipio' },
		{ data: 'provincia', name: 'provincia' },
		{ data: 'telefono', name: 'telefono' },
		{ data: 'modalidad_preventiva', name: 'modalidad_preventiva' },
		{ data: 'construccion', name: 'construccion', orderable: false, searchable: false, className: 'text-center' },
		{ data: 'autonomo', name: 'autonomo', orderable: false, searchable: false, className: 'text-center' },
	];
	$.App.DT.set({
		tableName: 'empresas-table',
		columnsDef: columns,
		urlList: '{!! route('empresas.rowsData') !!}',
		urlEdit: '{!! route('empresas.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('empresa');
});
</script>
@endpush
