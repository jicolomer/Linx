@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => $subject,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>Hola {{ $trabajador_nombre }}:</p>
        <p>Has sido invitado a darte de alta en la plataforma <strong>{{ config('app.name') }}</strong> de la empresa <strong>{{ $empresa_principal_razon_social }}</strong>.</p>
        <p>Una vez que hayas creado tu contraseña podrás acceder y participar en la <em>coordinación</em> del contrato: <strong>{{ $contrato_nombre }} (Ref: {{ $contrato_referencia }})</strong>.</p>
        <p>Pulsa el siguiente botón para crear tu nueva contraseña de acceso:</p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Crear nueva contraseña',
            'link' => url('password/reset/' . $token )
    ])

@stop

@section('footer')
    {!! $footer_text !!}
@stop
