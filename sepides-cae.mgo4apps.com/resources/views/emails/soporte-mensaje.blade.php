@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Nueva solicitud de soporte ' . $nombre_plataforma,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>El usuario <strong>{{ $usuario_nombre }} (#{{ $usuario_id }})</strong> ha enviado el siguiente mensaje:</p>

        <p><em>Asunto:</em> <strong>{{ $asunto }}</strong></p>

        <p><em>Mensaje:</em>

        <blockquote>
            {!! $mensaje !!}
        </blockquote>

        <small>(Email de respuesta: <strong>{{ $email_respuesta }}</strong>)</small>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Responder',
            'link' => 'http://cae.mgo4apps.com/soporte' .
            // 'link' => 'http://cae.app/soporte' .
                        '?email_respuesta=' . $email_respuesta .
                        '&asunto=Re: ' . $asunto .
                        '&usuario_nombre=' .$usuario_nombre
    ])

@stop

@section('footer')
    {!! $footer_text !!}
@stop
