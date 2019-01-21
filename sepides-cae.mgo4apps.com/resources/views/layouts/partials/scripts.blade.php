<script src="{{ asset('/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>
<script src="{{ asset('/plugins/datatables/datatables.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/datatables/datatables.checkboxes.js') }}" type="text/javascript"></script>
<script src="{{ asset('/js/third/bootstrap-notify.min.js') }}" type="text/javascript"></script>
{{-- <script src="{{ asset('/plugins/fastclick/fastclick.min.js') }}" type="text/javascript"></script> --}}
<script src="{{ asset('/plugins/bootbox/bootbox.min.js') }}" type="text/javascript"></script>
<script src="{{ asset('/plugins/datepicker/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('/plugins/datepicker/locales/bootstrap-datepicker.es.min.js') }}" charset="UTF-8"></script>
<script src="{{ asset('/plugins/iCheck/icheck.min.js') }}"></script>
<script src="{{ asset('/plugins/select2/select2.min.js') }}"></script>
<script src="{{ asset('/plugins/select2/es.js') }}"></script>
<script src="{{ asset('/js/third/admin-lte.min.js') }}" type="text/javascript"></script>
<script type="text/javascript">
    var app_config = {
        "nombre_app": "{{ config('app.name') }}",
        "nombre_corto": "{{ config('cae.nombre_corto') }}",
        "filas_tablas": {{ config('cae.filas_tablas') }},
        "filas_tablas_modal": {{ config('cae.filas_tablas_modal') }},
        "debug": {{ env('APP_DEBUG') == true ? 'true' : 'false' }},
    };
</script>
<script src="{{ asset('/js/app.js') }}" type="text/javascript"></script>
