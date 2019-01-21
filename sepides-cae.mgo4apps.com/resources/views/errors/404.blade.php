@extends('layouts.app')

@section('htmlheader_title')
    Error 404 - Página no encontrada
@endsection

@section('contentheader_title')
@endsection

@section('main-content')
<div class="error-page">
    <h2 class="headline text-yellow"><i class="fa fa-warning fa-lg text-yellow"></i></h2>
    <div class="error-content">
        <h3><strong>No se ha encontrado la página.</strong></h3>
        <p>No ha sido posible encontrar la página que has solicitado.</p>
        <p>Puedes regresar <a href="{{ url()->previous() }}">atrás</a> o ir al <a href='{{ route('home') }}'>panel principal</a>.</p>
        <p>Si consideras que hay un error, puedes <a href="{{ route('soporte.index') }}">enviar un mensaje</a> al departamento de <strong>soporte</strong> para notificarlo.</p>
    </div>
</div>
@endsection
