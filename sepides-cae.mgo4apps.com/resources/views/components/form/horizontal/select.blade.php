<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-{{ $width }}">
        {{ Form::select($name, $array, $selected ? $selected : old($selected), array_merge(['placeholder' => $placeholder, 'class' => 'form-control select2'], $attributes)) }}
    </div>
</div>
