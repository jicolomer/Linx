@extends('layouts.app')

@section('htmlheader_title')
	Permisos de la aplicación
@endsection

@section('contentheader_title')
	Permisos de la aplicación
@endsection

@section('main-content')
	<div class="row">
		<div class="col-xs-12">
			<div class="box">
				<div class="box-body">
					<table id="permissions-table" class="table table-bordered table-hover">
						<thead>
							<tr>
								<th width="15%">Tipo</th>
								<th width="25%">Permiso</th>
							@foreach ($roles as $id => $name)
								<th class="text-center">{{ $name }}</th>
							@endforeach
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
		{ data: 'tipo', name: 'tipo', cellType: 'th' },
		{ data: 'permiso', name: 'permiso', cellType: 'th' },
@foreach ($roles as $id => $name)
		{ data: 'rol_{{ $id }}', name: 'rol_{{ $id }}', orderable:false, searchable: false, className: 'text-center vcenter'},
@endforeach
		{ data: 'rowId', name: 'rowId', visible: false },
	];
	var dt = $.App.DT.set({
		tableName: 'permissions-table',
		columnsDef: columns,
		urlList: '{!! route('permisos.data') !!}',
		addActionColumn: false,
		dtOptions: {
			lengthChange: false,
			paginate: false,
			info: false,
		},
	});
@can('permisos.update')
	dt.on('draw', function() {
		$('input[type="checkbox"]').iCheck({
			checkboxClass: 'icheckbox_minimal-blue',
		});
		$('input[type="checkbox"]').on('ifToggled', function() {
			var parts = $(this).attr('id').split('_');
			var formData = {
				p: parts[1],
				r: parts[2],
				c: $(this).prop('checked'),
			};
			$.post(window.location, formData, function(data) {
				if (data.result === 'success') {
					$.App.notify.success(data.msg);
				} else {
					$.App.notify.error(data.msg);
				}
			})
			.always(function () {
				dt.ajax.reload();
			});
		});
	});
@endcan
});
</script>
@endpush
