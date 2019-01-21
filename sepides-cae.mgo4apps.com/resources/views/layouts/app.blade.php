<!DOCTYPE html>
<html lang="es">
@section('htmlheader')
    @include('layouts.partials.htmlheader')
@show
<body class="skin-blue sidebar-mini">
<div class="wrapper">
    @include('layouts.partials.mainheader')
    @include('layouts.partials.sidebar')

    <div class="content-wrapper">
        @include('layouts.partials.contentheader')
        <section class="content">
            @include('layouts.partials.floating-messages')
            @yield('main-content')
        </section>
    </div>

    @include('layouts.partials.controlsidebar')
    @include('layouts.partials.footer')
</div>
@section('main-scripts')
    @include('layouts.partials.scripts')
    @stack('scripts')
@show
</body>
</html>
