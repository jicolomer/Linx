<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-{{ $width }}">
        <div class="input-group">
            <input type="text" id="{{ $name }}-filename" class="form-control" readonly>
            <label class="input-group-btn">
                <span class="btn btn-warning btn-file"> Buscar... {{ Form::file($name, array_merge(['placeholder' => $placeholder, 'style' => 'display:none;'], $attributes)) }}</span>
            </label>
        </div>
    </div>
</div>
