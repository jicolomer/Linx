@extends('layouts.auth')

@section('htmlheader_title')
    Recuperación de contraseña
@endsection

@section('content')

<body class="login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/home') }}"><b>{{ config('app.name') }}</b> ({{ $empresa_principal_nombre }})</a>
        </div>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <strong>¡Vaya!</strong> Hay algunos problemas con su entrada.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="login-box-body">
            <p class="login-box-msg">Resetear Contraseña</p>
            <form action="{{ url('/password/email') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Email" name="email" value="{{ old('email') }}"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>

                <div class="row">
                    <div class="col-xs-2">
                    </div>
                    <div class="col-xs-8">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Enviar el enlace</button>
                    </div>
                    <div class="col-xs-2">
                    </div>
                </div>
            </form>

            <a href="{{ url('/login') }}">Iniciar sesión</a><br>

        </div>

        <div style="padding:15px;margin-top:10px;text-align:center">
        @if (file_exists(public_path('img/logo-empresa.png')))
            <div class="image">
                <img src="{{ asset('img/logo-empresa.png') }}" alt="{{ $empresa_principal_nombre }} logo" />
            </div>
        @endif
            <div class="image" style="margin-top:20px;">
                <img src="{{ asset('img/logo-mgo.png') }}" alt="MGO logo" />
            </div>
        </div>

    </div>

    @include('layouts.partials.scripts_auth')

</body>

@endsection
