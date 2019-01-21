<section class="content-header">
@if (isset($return_to))
    <a href="{{ $return_to }}" class= "btn btn-sm btn-primary pull-left"><i class="fa fa-arrow-left"></i> Regresar</a>
    <div class="pull-left">&nbsp;&nbsp;&nbsp;</div>
@endif
    <h1 style="float:left">
        @yield('contentheader_title', 'Page Header here')
        <small>@yield('contentheader_description')</small>
    </h1>
</section>
