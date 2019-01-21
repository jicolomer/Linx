<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-4">
        {{ Form::password($name, array_merge(['class' => 'form-control'], $attributes)) }}
    </div>
</div>
