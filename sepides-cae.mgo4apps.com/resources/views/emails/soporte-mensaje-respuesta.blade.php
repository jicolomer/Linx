@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => 'Respuesta a su solicitud de soporte',
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        {!! $mensaje !!}

        <p>
            Atentamente,<br />
            <strong><em>CAE - Departamento de soporte</em></strong>
        </p>

        <small><em>NOTA:</em> Si desea responder a este mensaje por favor utilice el formulario de contacto de la plataforma {{ $nombre_plataforma }}.</small>

    @include('beautymail::templates.sunny.contentEnd')

@stop

@section('footer')
    {!! $footer_text !!}
@stop
