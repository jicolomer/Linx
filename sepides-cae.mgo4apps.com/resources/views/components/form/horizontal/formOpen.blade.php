<div class="row">
    <div class="col-xs-12">
        <div class="box box-{{ $boxClass }}">
            <div class="box-header with-border">
                <h3 class="box-title">{{ $title }}</h3>
            </div>
        <?php
            $array = [ 'route' => $route, 'class' => 'form-horizontal' ];
            if ($files) {
                $array['files'] = true;
            }
            if ($model) {
                $array['method'] = 'PATCH';
            }
        ?>
        @if($model)
            {!! Form::model($model, $array) !!}
        @else
            {!! Form::open($array) !!}
        @endif
                <div class="box-body">
