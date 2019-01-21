<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-4">
        {{ Form::text($name, $value ? ($value == '-' ? '' : $value) : (old($name) ? old($name) : Date::now()->format('d/m/Y')),
            array_merge(['placeholder' => 'dd/mm/aaaa', 'class' => 'form-control', 'data-provide' => 'datepicker'], $attributes)) }}
    </div>
</div>
