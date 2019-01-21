<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'El campo &quot;:attribute&quot; debe ser aceptado.',
    'active_url'           => 'El campo &quot;:attribute&quot; no es una URL válida.',
    'after'                => 'El campo &quot;:attribute&quot; debe ser una fecha posterior a :date.',
    'alpha'                => 'El campo &quot;:attribute&quot; sólo puede contener letras.',
    'alpha_dash'           => 'El campo &quot;:attribute&quot; sólo puede contener letras, números y guiones (a-z, 0-9, -_).',
    'alpha_num'            => 'El campo &quot;:attribute&quot; sólo puede contener letras y números.',
    'array'                => 'El campo &quot;:attribute&quot; debe ser un array.',
    'before'               => 'El campo &quot;:attribute&quot; debe ser una fecha anterior a :date.',
    'between'              => [
        'numeric' => 'El campo &quot;:attribute&quot; debe ser un valor entre :min y :max.',
        'file'    => 'El archivo &quot;:attribute&quot; debe pesar entre :min y :max kilobytes.',
        'string'  => 'El campo &quot;:attribute&quot; debe contener entre :min y :max caracteres.',
        'array'   => 'El campo &quot;:attribute&quot; debe contener entre :min y :max elementos.',
    ],
    'boolean'              => 'El campo &quot;:attribute&quot; debe ser verdadero o falso.',
    'confirmed'            => 'El campo confirmación de &quot;:attribute&quot; no coincide.',
    'date'                 => 'El campo &quot;:attribute&quot; no corresponde con una fecha válida.',
    'date_format'          => 'El campo &quot;:attribute&quot; no corresponde con el formato de fecha :format.',
    'different'            => 'Los campos &quot;:attribute&quot; y :other han de ser diferentes.',
    'digits'               => 'El campo &quot;:attribute&quot; debe ser un número de :digits dígitos.',
    'digits_between'       => 'El campo &quot;:attribute&quot; debe contener entre :min y :max dígitos.',
    'email'                => 'El campo &quot;:attribute&quot; no corresponde con una dirección de e-mail válida.',
    'filled'               => 'El campo &quot;:attribute&quot; es obligatorio.',
    'exists'               => 'El campo &quot;:attribute&quot; no existe.',
    'image'                => 'El campo &quot;:attribute&quot; debe ser una imagen.',
    'in'                   => 'El campo &quot;:attribute&quot; debe ser igual a alguno de estos valores :values',
    'integer'              => 'El campo &quot;:attribute&quot; debe ser un número entero.',
    'ip'                   => 'El campo &quot;:attribute&quot; debe ser una dirección IP válida.',
    'json'                 => 'El campo &quot;:attribute&quot; debe ser una cadena de texto JSON válida.',
    'max'                  => [
        'numeric' => 'El campo &quot;:attribute&quot; debe ser :max como máximo.',
        'file'    => 'El archivo &quot;:attribute&quot; debe pesar :max kilobytes como máximo.',
        'string'  => 'El campo &quot;:attribute&quot; debe contener :max caracteres como máximo.',
        'array'   => 'El campo &quot;:attribute&quot; debe contener :max elementos como máximo.',
    ],
    'mimes'                => 'El campo &quot;:attribute&quot; debe ser un archivo de tipo :values.',
    'min'                  => [
        'numeric' => 'El campo &quot;:attribute&quot; debe tener al menos :min.',
        'file'    => 'El archivo &quot;:attribute&quot; debe pesar al menos :min kilobytes.',
        'string'  => 'El campo &quot;:attribute&quot; debe contener al menos :min caracteres.',
        'array'   => 'El campo &quot;:attribute&quot; no debe contener más de :min elementos.',
    ],
    'not_in'               => 'El campo &quot;:attribute&quot; seleccionado es invalido.',
    'numeric'              => 'El campo &quot;:attribute&quot; debe ser un numero.',
    'regex'                => 'El formato del campo &quot;:attribute&quot; es inválido.',
    'required'             => 'El campo &quot;:attribute&quot; es obligatorio',
    'required_if'          => 'El campo &quot;:attribute&quot; es obligatorio cuando el campo :other es :value.',
    'required_with'        => 'El campo &quot;:attribute&quot; es obligatorio cuando :values está presente.',
    'required_with_all'    => 'El campo &quot;:attribute&quot; es obligatorio cuando :values está presente.',
    'required_without'     => 'El campo &quot;:attribute&quot; es obligatorio cuando :values no está presente.',
    'required_without_all' => 'El campo &quot;:attribute&quot; es obligatorio cuando ningún campo :values están presentes.',
    'same'                 => 'Los campos &quot;:attribute&quot; y :other deben coincidir.',
    'size'                 => [
        'numeric' => 'El campo &quot;:attribute&quot; debe ser :size.',
        'file'    => 'El archivo &quot;:attribute&quot; debe pesar :size kilobytes.',
        'string'  => 'El campo &quot;:attribute&quot; debe contener :size caracteres.',
        'array'   => 'El campo &quot;:attribute&quot; debe contener :size elementos.',
    ],
    'string'               => 'El campo &quot;:attribute&quot; debe contener solo caracteres.',
    'timezone'             => 'El campo &quot;:attribute&quot; debe contener una zona válida.',
    'unique'               => 'El elemento &quot;:attribute&quot; ya está en uso.',
    'url'                  => 'El formato de &quot;:attribute&quot; no corresponde con el de una URL válida.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
