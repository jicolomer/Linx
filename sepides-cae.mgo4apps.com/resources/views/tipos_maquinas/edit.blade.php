@extends('layouts.app')

@section('htmlheader_title')
	Modificar tipo de máquina
@endsection

@section('contentheader_title')
	Modificar tipo de máquina
@endsection

@section('main-content')
	@include('tipos_documentos.select-tipos-documentos')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#t1" data-toggle="tab" >Tipo de Máquina</a></li>
							<li><a href="#t2" data-toggle="tab">Tipos de Documentos Asociados</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="t1">
								{!! Form::model($model, ['route' => ['tipos-maquinas.update', $model->id], 'method' => 'PATCH', 'class' => 'form-horizontal']) !!}
									{{ Form::fhText('id', null, null, false, 4, $model->id, ['readonly' => true]) }}
									{{ Form::fhText('nombre', null, null, true) }}
									{{ Form::fhTextarea('notas') }}
									<div class="box-footer">
										<button type="submit" class="btn btn-primary">Guardar cambios</button>
									</div>
								</form>
							</div>
							<div class="tab-pane" id="t2">
								<div class="box-header">
									<h3 class="box-title">Tipos de Documentos Asociados</h3>
									<div class="box-tools">
										<button type="button" class="btn btn-primary bootstrap-modal-form-open" data-toggle="modal" data-target="#tipos-documentos-modal-dialog">Asociar &nbsp;<i class="fa fa-plus"></i></button>
									</div>
								</div>
								<div class="box-body">
									<table id="documentos-table" class="table table-bordered table-hover">
										<thead>
											<tr>
												<th class="text-right">ID</th>
												<th>Referencia</th>
												<th>Nombre</th>
												<th>Ámbito</th>
												<th>Tipo Caducidad</th>
												<th>Palabras Clave</th>
												<th>¿Obligatorio?</th>
												<th></th>
											</tr>
										</thead>
										<tbody></tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script src="{{ asset('/js/select-tipos-documentos.js') }}" type="text/javascript"></script>
<script>
$(function() {
	$.STD().init(
		'/tipos-maquinas',
		'{!! route('tipos-documentos.rowsData') !!}?a=MAQ',
		'documentos-table',
		'{!! route('tipos-maquinas.tiposDocumentosData') !!}',
		null,
		true
	);
});
</script>
@endpush
