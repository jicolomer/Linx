<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-10">
        {{ Form::email($name, $value ? $value : old($name), array_merge(['placeholder' => 'ejemplo@email.com', 'class' => 'form-control'], $attributes)) }}
    </div>
</div>
