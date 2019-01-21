@extends('beautymail::templates.sunny')

@section('content')

    @include ('beautymail::templates.sunny.heading' , [
        'heading' => $subject,
        'level' => 'h1',
    ])

    @include('beautymail::templates.sunny.contentStart')

        <p>Hola {{ $trabajador_nombre }}:</p>
        <p>Tu empresa ha sido invitada a participar en la <em>coordinación</em> del contrato: <strong>{{ $contrato_nombre }} (Ref: {{ $contrato_referencia }})</strong> como <em>contratista</em>.</p>

    @include('beautymail::templates.sunny.contentEnd')

    @include('beautymail::templates.sunny.button', [
            'title' => 'Acceder a la plataforma',
            'link' => url('/login' )
    ])

@stop

@section('footer')
    {!! $footer_text !!}
@stop
