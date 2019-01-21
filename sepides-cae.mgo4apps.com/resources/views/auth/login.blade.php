@extends('layouts.auth')

@section('htmlheader_title')
    Iniciar Sesión
@endsection

@section('content')
<body class="hold-transition login-page">
    <div class="login-box">
        <div class="login-logo">
            <a href="{{ url('/home') }}"><b>{{ config('app.name') }}</b> ({{ $empresa_principal_nombre }})</a>
        </div>
    @if (count($errors) > 0)
        <div class="alert alert-danger">
        @if (count($errors) == 1)
            <p>{!! $errors->all()[0] !!}</p>
        @else
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{!! $error !!}</li>
                @endforeach
            </ul>
        @endif
        </div>
    @endif
        <div class="login-box-body">
            <p class="login-box-msg"> Inicie sesión para acceder</p>
            <form action="{{ url('/login') }}" method="post">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="form-group has-feedback">
                    <input type="email" class="form-control" placeholder="Correo Electrónico" name="email"/>
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">
                    <input type="password" class="form-control" placeholder="Contraseña" name="password"/>
                    <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                    <div class="col-xs-8">
                        <div class="checkbox icheck">
                            <label>
                                <input type="checkbox" name="remember" checked="checked"> Mantenerme logueado
                            </label>
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <button type="submit" class="btn btn-primary btn-block btn-flat">Entrar</button>
                    </div>
                </div>
            </form>

            <a href="{{ url('/password/reset') }}">Olvidé mi contraseña</a><br>

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
