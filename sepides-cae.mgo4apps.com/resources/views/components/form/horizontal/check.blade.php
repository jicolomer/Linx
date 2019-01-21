<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-1">
        {{ Form::checkbox($name, null, $selected, array_merge(['class' => 'form-control minimal'], $attributes)) }}
    </div>
</div>
