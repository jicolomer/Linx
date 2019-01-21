@extends('layouts.app')

@section('htmlheader_title')
	Contratos
@endsection

@section('contentheader_title')
	Contratos
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Contratos</h3>
						@can('contratos.create')
							<div class="box-tools"><a href="{{ route('contratos.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a></div>
						@endcan
						</div>
						<div class="box-body">
							<table id="contratos-table">
								<thead>
									<tr>
										<th class="text-center" data-priority="5">ID</th>
										<th data-priority="1">Ref.</th>
										<th data-priority="3">Nombre</th>
										<th>Firma</th>
										<th>Inicio</th>
										<th>Fin</th>
										<th>Tipo</th>
										<th data-priority="4">Responsable</th>
										<th data-priority="2">Contratista</th>
										<th>Doc.</th>
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
		{ data: 'id', name: 'contratos.id', className: 'text-right', cellType: 'th' },
		{ data: 'referencia', name: 'contratos.referencia', cellType: 'th' },
		{ data: 'nombre', name: 'contratos.nombre' },
		{ data: 'fecha_firma', name: 'contratos.fecha_firma', orderData: 10 },
		{ data: 'fecha_inicio_obras', name: 'contratos.fecha_inicio_obras', orderData: 11 },
		{ data: 'fecha_fin_obras', name: 'contratos.fecha_fin_obras', orderData: 12 },
		{ data: 'tipo_contrato_column', name: 'tipo_contrato_column', className: 'text-center', orderData: 13 },
		{ data: 'responsable', name: 'responsable' },
		{ data: 'contratista', name: 'contratista' },
		{ data: 'status_doc', name: 'status_doc', className: 'text-center vcenter' },
		{ data: 'fecha_firma_raw', name: 'fecha_firma_raw', className: 'hide-in-colvis', visible: false },
		{ data: 'fecha_inicio_raw', name: 'fecha_inicio_raw', className: 'hide-in-colvis', visible: false },
		{ data: 'fecha_fin_raw', name: 'fecha_fin_raw', className: 'hide-in-colvis', visible: false },
		{ data: 'tipo_contrato_id', name: 'contratos.tipo_contrato_id', className: 'hide-in-colvis', visible: false },
	];
	$.App.DT.set({
		tableName: 'contratos-table',
		columnsDef: columns,
		urlList: '{!! route('contratos.rowsData') !!}',
		urlEdit: '{!! route('contratos.edit', 'XX') !!}',
		addActionColumn: false,
	});
});
</script>
@endpush
