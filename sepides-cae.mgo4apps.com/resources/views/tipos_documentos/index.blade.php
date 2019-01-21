@extends('layouts.app')

@section('htmlheader_title')
	Tipos de Documentos
@endsection

@section('contentheader_title')
	Tipos de Documentos
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Tipos de Documentos</h3>
							<div class="box-tools">
								<a href="{{ route('tipos-documentos.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a>
								&nbsp;
								<button class="btn archive-button"><i class="fa fa-archive"></i> Archivados</button>
							</div>
						</div>
						<div class="box-body">
							<table id="documentos-table">
								<thead>
									<tr>
										<th class="text-center">ID</th>
										<th data-priority="1">Referencia</th>
										<th data-priority="2">Nombre</th>
										<th>Notas</th>
										<th>Ámbito</th>
										<th>Caducidad</th>
										<th>Palabras clave</th>
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
		{ data: 'referencia', name: 'referencia', cellType: 'th', width: '10%' },
		{ data: 'nombre', name: 'nombre', cellType: 'th', width: '20%' },
		{ data: 'notas', name: 'notas', width: '100px' },
		{ data: 'ambito', name: 'ambito', width: '30px' },
		{ data: 'tipo_caducidad', name: 'tipo_caducidad', width: '20px' },
		{ data: 'tags', name: 'tags', width: '20%' },
	];
	$.App.DT.set({
		tableName: 'documentos-table',
		columnsDef: columns,
		urlList: '{!! route('tipos-documentos.rowsData') !!}',
		urlEdit: '{!! route('tipos-documentos.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('tipo de documento');
});
</script>
@endpush
