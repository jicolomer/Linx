<header class="main-header">
    <a href="{{ url('/') }}" class="logo">
        <span class="logo-mini">{{ config('app.name') }}</span>
        <span class="logo-lg"><b>{{ config('app.name') }}</b> ({{ config('cae.nombre_corto') }})</span>
    </a>

    <nav class="navbar navbar-static-top" role="navigation">
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </a>
        <div class="navbar-custom-menu">
            <ul class="nav navbar-nav">
            @if (Auth::guest())
                <li><a href="{{ url('/login') }}">Iniciar sesión</a></li>
            @else
                @if (! Auth::user()->isControl())
                <li class="dropdown notifications-menu">
                    <a href="{!! route('empresa') !!}"><small><strong>{{ Session::get('user_empresa_display_name') }}</strong></small></a>
                </li>
                @endif
                <li class="dropdown notifications-menu">
                    <?php $avisos = Auth::user()->avisos()->wherePivot('leido', '=', false)->count(); ?>
                    <a href="/"{!! $avisos > 0 ? 'data-title="Tiene ' . $avisos . ' aviso' . ($avisos > 1 ? 's' : '') . ' sin leer." data-toggle="tooltip" data-placement="bottom"' : "" !!}>
                        <i class="fa fa-bell-o"></i>
                        <span class="badge label-{{ $avisos < 6 ? 'success' : ($avisos < 11 ? 'warning' : 'danger') }}">{{ $avisos }}</span>
                    </a>
                </li>
                <li class="dropdown user user-menu">
                    <a href="#" class="dropdown-toggle text-center" data-toggle="dropdown" style="padding:5px 15px">
                        <span class="hidden-xs" style="display:block">{{ Auth::user()->nombre }}&nbsp;</span>
                        <?php
                            $color = 'gray';
                        if (Auth::user()->isRole('tecnico')) {
                            $color = 'green';
                        } elseif (Auth::user()->isRole('responsable')) {
                            $color = 'yellow';
                        } elseif (Auth::user()->isRole('control')) {
                            $color = 'teal';
                        } elseif (Auth::user()->isRole('externo')) {
                            $color = 'red';
                        }
                            $role = Auth::user()->roles()->first()->name
                        ?>
                        <small class="label bg-{{ $color }}" style="position:static">{{ $role }}</small>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="user-header">
                            <p>
                                {{ Auth::user()->nombre }}
                                <small><em>{{ Auth::user()->email }}</em></small>
                                <span class="label bg-{{ $color }}" style="position:static">{{ $role }}</span>
                                <br/>
                                <br/>
                                <small>Miembro desde el {{ Date::parse(Auth::user()->created_at)->format('d \d\e F \d\e Y') }}</small>
                            </p>
                        </li>
                        <li class="user-footer">
                            <div class="pull-right">
                                <a href="{{ url('/logout') }}" class="btn btn-default btn-flat" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                                    Cerrar sesión
                                </a>
                                <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                    {{ csrf_field() }}
                                </form>
                            </div>
                        </li>
                    </ul>
                </li>
            @endif
            </ul>
        </div>
    </nav>
</header>
