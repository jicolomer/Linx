@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => $nombre_plataforma,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>Hola {{ $usuario->nombre }}:</p>
        <p>Debes crear una nueva contraseña para poder acceder a nuestra plataforma. Puedes pulsar en el botón de abajo para hacerlo.</p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Crear nueva contraseña',
            'link' => url('password/reset/' . $token )
    ])

@stop

@section('footer')
    {!! $footer_text !!}
@stop
