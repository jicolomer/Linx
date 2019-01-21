@extends('layouts.app')

@section('htmlheader_title')
	Panel
@endsection

@section('contentheader_title')
	Panel de control
@endsection

@section('main-content')
	<div class="row">
		<div class="col-lg-3 col-xs-6">
	        <div class="small-box bg-light-blue">
	        	<div class="inner">
	            	<h3>{{ $num_contratos }}</h3>
					<p>Contratos</p>
	            </div>
	            <div class="icon">
	            	<i class="ion ion-document-text"></i>
	            </div>
	            <a href="{{ route('contratos.index') }}" class="small-box-footer">Ir a Contratos <i class="fa fa-arrow-circle-right"></i></a>
	        </div>
	    </div>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-green">
				<div class="inner">
					<h3>{{ $num_empresas }}</h3>
					<p>{{ Auth::user()->isExterno() ? 'Subcontratistas' : 'Empresas' }}</p>
				</div>
				<div class="icon">
					<i class="fa fa-cubes"></i>
				</div>
				<a href="{{ Auth::user()->isExterno() ? route('empresa') : route('empresas.index') }}" class="small-box-footer">Ir a Empresas <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-yellow">
				<div class="inner">
					<h3>{{ $num_trabajadores }}</h3>
					<p>Trabajadores</p>
				</div>
				<div class="icon">
					<i class="ion ion-person-stalker"></i>
				</div>
				<a href="{{ Auth::user()->isExterno() ? route('empresa').'#t3' : route('trabajadores.index') }}" class="small-box-footer">Ir a Trabajadores <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
		<div class="col-lg-3 col-xs-6">
			<div class="small-box bg-red">
				<div class="inner">
					<h3>{{ $num_maquinas }}</h3>
					<p>Máquinas</p>
				</div>
				<div class="icon">
					<i class="fa fa-truck"></i>
				</div>
				<a href="{{ Auth::user()->isExterno() ? route('empresa').'#t4' : route('maquinas.index') }}" class="small-box-footer">Ir a Máquinas <i class="fa fa-arrow-circle-right"></i></a>
			</div>
		</div>
	</div>
	<div class="row">
		<section class="col-lg-{{ Auth::user()->isExterno() ? '12' : '7' }} connectedSortable ui-sortable">
			<div class="box box-primary">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-bell-o"></i>&nbsp; Avisos</h3>
					<div class="box-tools pull-right">
                		<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
              		</div>
				</div>
				<div class="box-body">
					<table id="avisos-table">
						<thead>
							<tr>
								<th style="padding-left:5px;padding-right:5px;width:20px"></th>
								<th>Aviso</th>
								<th>Fecha</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>
		</section>

	@if (! Auth::user()->isExterno())
		<section class="col-lg-5 connectedSortable ui-sortable">
			<div class="box">
				<div class="box-header with-border">
					<h3 class="box-title"><i class="fa fa-ellipsis-v"></i> &nbsp; Últimas actualizaciones</h3>
				</div>
				<div class="box-body changelog" id="changelog-box">
					{!! $changelog !!}
				</div>
			</div>
		</section>
	@endif
	</div>
@endsection

@push('scripts')
<script src="{{ asset('/plugins/slimScroll/jquery.slimscroll.min.js') }}" type="text/javascript"></script>
<script>
$(function() {
	$('#changelog-box').slimScroll({
		height: '650px',
	});
	var columns = [
		{ data: 'leido_icon', name: 'leido_icon', orderData: 3, className: 'text-center vcenter' },
		{ data: 'texto', name: 'texto'},
		{ data: 'fecha', name: 'fecha', orderData: 4 },
		{ data: 'leido', name: 'leido', visible: false },
		{ data: 'created_at', name: 'created_at', visible: false },
	];
	var dt = $.App.DT.set({
		tableName: 'avisos-table',
		columnsDef: columns,
		urlList: '{!! route('avisos.data') !!}',
		urlEdit: '{!! route('avisos.go', 'XX') !!}',
		addActionColumn: false,
		shortList: true
	});
	dt.order([2, 'desc']).draw();
});
</script>
@endpush
