@extends('layouts.app')

@section('htmlheader_title')
    Error 401 - No tienes permiso para acceder
@endsection

@section('contentheader_title')
@endsection

@section('main-content')
<div class="error-page">
    <h2 class="headline text-red"><i class="fa fa-warning fa-lg text-red"></i></h2>
    <div class="error-content">
        <h3><strong>No tienes permiso para acceder.</strong></h3>
        <p>No tienes permiso para acceder a la página que has solicitado.</p>
        <p>Si crees que esto es un error, por favor <strong>infórmalo a uno de los administradores</strong> de la aplicación.</p>
        <p>Puedes regresar al panel principal pulsando <a href='{{ route('home') }}'>aquí</a>.</p>
    </div>
</div>
@endsection
