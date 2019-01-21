<aside class="main-sidebar">
    <section class="sidebar">
        <ul class="sidebar-menu">
        @can('dashboard.view')
            <li class="header"><i class='fa fa-dashboard'></i>&nbsp; PANEL</li>
            <li {{{ (Request::is('/') ? 'class=active' : '') }}}><a href="/"><i class='fa fa-dashboard'></i> <span>Panel</span></a></li>
        @endcan
        @can('contratos.view')
            <li class="header"><i class='fa fa-legal'></i>&nbsp; CONTRATOS</li>
            <li {{{ (Request::segment(1) == 'contratos') ? 'class=active' : '' }}}><a href="{{ route('contratos.index') }}"><i class='fa fa-legal'></i> <span>Contratos</span></a></li>
        @endcan
        @canAtLeast(['empresas-global.view', 'empresas.view'])
        @can('empresas-global.view')
            <li class="header"><i class='fa fa-home'></i>&nbsp; EMPRESA PRINCIPAL</li>
        @else
            <li class="header"><i class='fa fa-home'></i>&nbsp; MI EMPRESA</li>
        @endcan
        @endCanAtLeast
        @can('empresas.view')
            <li {{{ (Request::segment(1) == 'empresa') ? 'class=active' : '' }}}><a href="{{ route('empresa') }}"><i class='fa fa-circle-o'></i> Datos Empresa</a></li>
        @endcan
        @can('centros.view')
            <li {{{ (Request::segment(1) == 'centros') ? 'class=active' : '' }}}><a href="{{ route('centros.index') }}"><i class='fa fa-industry'></i> <span>Centros de trabajo</span></a></li>
        @endcan
        @cannot('empresas-global.view')
        @can('empresas.view')
            <li {{{ (Request::segment(1) == 'empresas') ? 'class=active' : '' }}}><a href="{{ route('empresas.index') }}"><i class='fa fa-cubes'></i> Subcontratistas</a></li>
        @endcan
        @endcannot
        @can('acceso.view')
            <li class="header"><i class='fa fa-external-link-square'></i>&nbsp; CONTROL ACCESOS</li>
            <li {{{ (Request::segment(1) == 'control-acceso') ? 'class=active' : '' }}}><a href="{{ route('control-acceso') }}"><i class='fa fa-hand-paper-o'></i> <span>Control Accesos</span></a></li>
        @endcan
        @can('empresas-global.view')
            <li class="header"><i class='fa fa-external-link-square'></i>&nbsp; EMPRESAS EXTERNAS</li>
            <li {{{ (Request::segment(1) == 'empresas') ? 'class=active' : '' }}}><a href="{{ route('empresas.index') }}"><i class='fa fa-cubes'></i> Empresas</a></li>
            <li {{{ (Request::segment(1) == 'trabajadores') ? 'class=active' : '' }}}><a href="{{ route('trabajadores.index') }}"><i class='fa fa-user'></i> Trabajadores</a></li>
            <li {{{ (Request::segment(1) == 'maquinas') ? 'class=active' : '' }}}><a href="{{ route('maquinas.index') }}"><i class='fa fa-bus'></i> Maquinaria</a></li>
        @endcan
        @canAtLeast(['tipos-documentos.view'. 'tipos-maquinas.view', 'tipos-contratos.view', 'usuarios.view', 'usuarios-global.view', 'configs.view', 'permisos.view'])
            <li class="header"><i class='fa fa-gears'></i>&nbsp; CONFIGURACIÓN</li>
        @endCanAtLeast
        @can('tipos-contratos.view')
            <li {{{ (Request::segment(1) == 'tipos-contratos') ? 'class=active' : '' }}}><a href="{{ route('tipos-contratos.index') }}"><i class='fa fa-list-ol'></i> Tipos de Contratos</a></li>
        @endcan
        @can('tipos-maquinas.view')
            <li {{{ (Request::segment(1) == 'tipos-maquinas') ? 'class=active' : '' }}}><a href="{{ route('tipos-maquinas.index') }}"><i class='fa fa-trademark'></i> Tipos de Máquinas</a></li>
        @endcan
        @can('tipos-documentos.view')
            <li {{{ (Request::segment(1) == 'tipos-documentos') ? 'class=active' : '' }}}><a href="{{ route('tipos-documentos.index') }}"><i class='fa fa-files-o'></i> <span>Tipos de Documentos</span></a></li>
        @endcan
        @canAtLeast(['usuarios.view', 'usuarios-global.view'])
            <li {{{ (Request::segment(1) == 'usuarios') ? 'class=active' : '' }}}><a href="{{ route('usuarios.index') }}"><i class='fa fa-users'></i> Usuarios</a></li>
        @endCanAtLeast
        @can('permisos.view')
            <li {{{ (Request::segment(1) == 'permisos') ? 'class=active' : '' }}}><a href="{{ route('permisos') }}"><i class='fa fa-check-circle'></i> Permisos CAE</a></li>
        @endcan
        @can('configs.view')
            <li {{{ (Request::segment(1) == 'config') ? 'class=active' : '' }}}><a href="{{ route('config') }}"><i class='fa fa-cog'></i> Configuración CAE</a></li>
        @endcan
            <li class="header"><i class='fa fa-question-circle'></i>&nbsp; SOPORTE</li>
            <li {{{ (Request::segment(1) == 'soporte') ? 'class=active' : '' }}}><a href="{{ route('soporte.index') }}"><i class='fa fa-envelope'></i> Contactar</a></li>
        </ul>
    </section>
</aside>
