@extends('layouts.app')

@section('htmlheader_title')
	Centros de trabajo
@endsection

@section('contentheader_title')
	Centros de trabajo
@endsection

@section('main-content')
			<div class="row">
				<div class="col-xs-12">
					<div class="box box-primary">
						<div class="box-header">
							<h3 class="box-title">Listado de Centros</h3>
							<div class="box-tools"><a href="{{ route('centros.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Nuevo</a></div>
						</div>
						<div class="box-body">
							<table id="centros-table">
								<thead>
									<tr>
										<th class="text-center">ID</th>
										<th>Nombre</th>
										<th>Dirección</th>
										<th>Código Postal</th>
										<th>Municipio</th>
										<th>Email Centro</th>
										<th>Teléfono Centro</th>
										<th>Persona Contacto</th>
										<th>Teléfono Contacto</th>
										<th>Email Contacto</th>
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
		{ data: 'direccion', name: 'direccion' },
		{ data: 'codigo_postal', name: 'codigo_postal' },
		{ data: 'municipio', name: 'municipio' },
		{ data: 'email_centro', name: 'email_centro' },
		{ data: 'telefono_centro', name: 'telefono_centro' },
		{ data: 'persona_contacto', name: 'persona_contacto' },
		{ data: 'telefono_contacto', name: 'telefono_contacto' },
		{ data: 'email_contacto', name: 'email_contacto' },
	];
	$.App.DT.set({
		tableName: 'centros-table',
		columnsDef: columns,
		urlList: '{!! route('centros.rowsData') !!}',
		urlEdit: '{!! route('centros.edit', 'XX') !!}'
	});
	$.App.DT.setDeleteDialog('centro');
});
</script>
@endpush
