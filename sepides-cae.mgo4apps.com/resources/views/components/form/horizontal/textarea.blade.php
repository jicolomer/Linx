<div class="form-group{{ $errors->has($name) ? ' has-error' : '' }}{{ $required ? ' required' : '' }}" id="{{ $name }}-group">
    {{ Form::label($name, $title, ['class' => 'control-label col-sm-2']) }}
    <div class="col-sm-{{ $width }}">
        <?php
            if (!array_key_exists("size", $attributes)) {
                $attributes = array_merge(['size' => '50x5'], $attributes);
            }
            $attributes = array_merge(['placeholder' => $placeholder, 'class' => 'form-control'], $attributes);
        ?>
        {{ Form::textarea($name, $value ? $value : old($name), $attributes) }}
    </div>
</div>
