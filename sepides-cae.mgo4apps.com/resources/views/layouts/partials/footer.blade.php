<footer class="main-footer">
    <div class="pull-right image" style="margin-top:-13px;">
        <img src="{{ asset('img/logo-mgo-small.png') }}" alt="MGO logo" />
    </div>
@if (file_exists(public_path('img/logo-empresa-small.png')))
    <div class="pull-right image" style="margin-top:-13px;margin-right:10px;">
        <img src="{{ asset('img/logo-empresa-small.png') }}" alt="{{ config('cae.nombre_corto') }} logo" />
    </div>
@endif
    <strong>{{ config('app.name') }}</strong> ({{ config('cae.nombre_corto') }}) - Versión {{ config('cae.version') }} - Copyright &copy; {{ date("Y") }}.
</footer>
