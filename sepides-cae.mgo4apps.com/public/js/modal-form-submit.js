$.ModalForm = {};
(function($) {
    "use strict";

    // DM
    var $errors_box = $('#errors-box');
    var $errors_ul = $('#errors-box ul');
    var _debug = false;

    // Reset errors when opening modal.
    $('.bootstrap-modal-form-open').click(function() {
        resetModalFormErrors();
    });
    // Prepare reset.
    function resetModalFormErrors() {
        $('.form-group').removeClass('has-error');
        $('.form-group').find('.help-block').remove();
        // DM
        if ($errors_box != null) {
            $errors_ul.empty();
            $errors_box.hide();
        }
    }

    function submitForm($form) {
        // Set vars.
        // var form = $(this);
        var url = $form.attr('action');
        var submit = $form.find('[type=submit]');
        // DM - Laravel TOKEN for requests
        var _token = $('meta[name="csrf-token"]').attr('content');
        // Check for file inputs.
        if ($form.find('[type=file]').length) {
            // If found, prepare submission via FormData object.
            var input = $form.serializeArray();
            var data = new FormData();
            var contentType = false;
            // Append input to FormData object.
            $.each(input, function(index, input) {
                data.append(input.name, input.value);
            });
            // DM - Laravel TOKEN for requests
            data.append('_token', _token);
            // Append files to FormData object.
            $.each($form.find('[type=file]'), function(index, input) {
                if (input.files.length == 1) {
                    data.append(input.name, input.files[0]);
                } else if (input.files.length > 1) {
                    data.append(input.name, input.files);
                }
            });
        } else {
            // If no file input found, do not use FormData object (better browser compatibility).
            var data = $form.serialize();
            var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
            // DM - Laravel TOKEN for requests
            data += "&_token=" + _token;
            if (_debug) {
                console.log(data);
            }
        }
        // Please wait.
        if (submit.is('button')) {
            var submitOriginal = submit.html();
            submit.html('Por favor, espere...');
        } else if (submit.is('input')) {
            var submitOriginal = submit.val();
            submit.val('Por favor, espere...');
        }
        // Request.
        $.ajax({
            type: "POST",
            url: url,
            data: data,
            dataType: 'json',
            cache: false,
            contentType: contentType,
            processData: false
        // Response.
        }).always(function(response, status) {
            if (_debug) {
                console.log(response);
            }
            // Reset errors.
            resetModalFormErrors();
            // Check for errors.
            if (response.status == 422) {
                var errors = $.parseJSON(response.responseText);
                // Iterate through errors object.
                $.each(errors, function(field, message) {
                    if (_debug == false) {
                        console.error(field+': '+message);
                    }
                    var formGroup = $('[name='+field+']', $form).closest('.form-group');
                    // DM
                    if ($errors_box == null) {
                        formGroup.addClass('has-error').append('<p class="help-block">'+message+'</p>');
                    } else {
                        formGroup.addClass('has-error');
                        $errors_ul.append('<li>'+message+'</li>');
                    }
                });
                // DM
                if ($errors_box != null) {
                    $errors_box.show();
                }
                // Reset submit.
                if (submit.is('button')) {
                    submit.html(submitOriginal);
                } else if (submit.is('input')) {
                    submit.val(submitOriginal);
                }
            // If successful, reload.
            } else {
                if (_debug == false) {
                    location.reload();
                }
            }
        });
    }

    $.ModalForm = {
        debug: function () {
            _debug = true;
        },
        attachTo: function($form) {
            $form.on('submit', function(e) {
                e.preventDefault();
                submitForm($(this), e);
            });
        },
        submit: function($form) {
            submitForm($form);
        },
    };
})(window.jQuery);
