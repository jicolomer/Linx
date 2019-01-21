// CAE APP
$.App = {};

$(function () {
    "use strict";
    // Set up the object
    __initialize();
    // DATATABLES
    $.App.DT.init();
    // Boostrap Notify defaults
    $.notifyDefaults({
        newest_on_top: true,
        allow_dismiss: false,
        url_taget: '_self',
        offset: 20,
        z_index: 1051,
        animate: {
            enter: 'animated bounceInDown',
            exit: 'animated fadeOutRight',
        },
    });
    // Bootbox defaults
    bootbox.setDefaults({
        locale: "es",
        show: true,
        closeButton: true,
        animate: true,
    });
    // PARA SELECCIÓN DE TABS
    var url = document.location.toString();
    if (url.match('#')) {
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
    }
    $('.nav-tabs a').on('shown.bs.tab', function (e) {
        window.location.hash = e.target.hash;
    });
    // DATEPICKER default settings
    $.fn.datepicker.defaults.language = "es";
    $.fn.datepicker.defaults.autoclose = true;
    $.fn.datepicker.defaults.todayHighlight = true;
    $.fn.datepicker.defaults.clearBtn = true;
    $.fn.datepicker.defaults.format = "dd/mm/yyyy";
    // File select EVENT
    $(document).on('change', ':file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    $(':file').on('fileselect', function(event, numFiles, label) {
        console.log($(this).attr('id'));
        $('#' + $(this).attr('id') + '-filename').val(label);
    });
    // SELECT 2
    $('select.select2').each(function(index) {
        $(this).select2({
            placeholder: $(this).find('option:first').text(),
            width: '100%',
            allowClear: true,
        });
    });
    // iCheck
    $('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
    });
    $('input[type="radio"]').iCheck({
        radioClass: 'iradio_minimal-blue',
    });
    // TOOLTIPS
    $('[data-toggle="tooltip"]').tooltip();
    // DM - Añadir diálogo de confirmación para operaciones contra el server:
    //       eliminar, resetear, etc.
    //
    //      Añade gestión del evento 'click' a un <a>, donde pide la confirmación
    //      y si se pulsa 'Aceptar' va al 'href' del <a>
    //
    //      Parámetros:
    //          class: ask-for-confirmation
    //          data-ask: texto que aparecerá preguntando en el diálogo de confirmación
    //          data-loading: texto que se cambiará en el botón del <a> para
    //                        indicar al usuario que se está realizando la operación
    //
    $('a.ask-for-confirmation').on('click', function (e) {
        var _msg = $(this).data('msg');
        var _ask = $(this).data('ask');
        var _loading = $(this).data('loading');

        function showConfirmationDialog(yes_callback) {
            var msg = "<h3>" + _msg + "</h3>";
            if (_ask == null) {
                msg += "¿Está seguro de querer hacerlo?";
            } else {
                msg += _ask;
            }
            bootbox.dialog({
                title: "Por favor, confirme",
                message: msg,
                className: "modal-danger",
                onEscape: function() {},
                buttons: {
                    si: {
                        label: "Sí",
                        className: "btn-outline pull-right",
                        callback: yes_callback
                    },
                    no: {
                        label: "¡No!",
                        className: "btn-outline pull-left",
                    },
                }
            });
        }
        e.preventDefault();
        showConfirmationDialog(function() {
            if (_loading != null) {
                $(e.currentTarget).removeClass('btn-default').addClass('btn-warning');
                $(e.currentTarget).html('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>  ' + _loading);
            }
            window.location.href = $(e.currentTarget).attr('href');
        });
    });
    $('a.ask-for-confirmation').each(function() {
        $(this).addClass('btn btn-default');
    });
    // DM - Chequea si la sesión ha caducado cada 2 minutos
    if (app_config.debug == false) {
        setInterval(function checkSession() {
            $.get('/check-session', function(data) {
                if (data.guest == true) {
                    location.reload();
                }
            });
        }, 2*60*1000);
    }
    // CSRF Token para todas las llamadas POST con ajax
    $.ajaxSetup({
        headers: { 'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content') }
    });
});
// DM - Listener para errores '401 - Unauthorized' en ajax debido a la
//      caducidad de la sesión. Si ha caducado recarga la página lo que hace que
//      el middleware rebote al login
$(document).ajaxError(function(event, jqxhr, settings, exception) {
    if (exception == 'Unauthorized') {
        location.reload();
    }
});
function isHTML(str) {
    var doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(function (node, idx, nodes) {
        return node.nodeType === 1;
    });
}
function mergeObject(target) {
    for (var i = 1; i < arguments.length; i++) {
        var source = arguments[i];
        for (var key in source) {
            if (source.hasOwnProperty(key)) {
                target[key] = source[key];
            }
        }
    }
    return target;
}
// Initialize Objects
function __initialize() {
    "use strict";
    // DATATABLES
    $.App.DT = {
        init: function () {
            // Desactivo los errores para gestionarlos con la sesión
            // $.fn.dataTable.ext.errMode = 'none';
            // Defaults
            $.extend($.fn.dataTable.defaults, {
                processing: true,
                serverSide: false,
                deferRender: true,
                responsive: true,
                stateSave: true,
                pageLength: app_config.filas_tablas,
                language: {
                    url: "/plugins/datatables/spanish.lang",
                    select: {
                                rows: {
                                    _: "%d filas seleccionadas",
                                    0: "",
                                    1: "1 fila seleccionada"
                                }
                    }
                },
            });
            // FIX: para tablas 'ocultas' en un tab al cargar la página
            //      que hace que las columnas con ancho fijo no lo tuviesen
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                var tables = $.fn.dataTable.fnTables(true);
                if ( tables.length > 0 ) {
                    tables.forEach(function(table) {
                        $(table).dataTable().fnAdjustColumnSizing();
                    });
                }
            });
        },
        // Configura el DT
        set: function (options) {
            var defaultOptions = {
                showButtons: true,
                addActionColumn: true,
                buttonsInActionColumn: 2,
                selectType: false,
                shortList: false,
            };
            // options = Object.assign({}, defaultOptions, options);
            options = mergeObject({}, defaultOptions, options);
            options.urlListArchive = options.urlList + '?h=1';
            var $table = $('#' + options.tableName);
            $table.addClass('table table-bordered table-hover');
            var dtOptions = {
                ajax: options.urlList,
                initComplete : function (settings, json) {
                    $('#' + options.tableName + '_wrapper .col-sm-6').each(function(index) {
                        $(this).removeClass('col-sm-6').addClass((index == 0) ? 'col-sm-8' : 'col-sm-4');
                    });
                    if (options.showButtons) {
                        table.buttons().container().appendTo( $('#' + options.tableName + '_wrapper .col-sm-8'));
                    }
                    $('#' + options.tableName + '_length select').select2({
                        minimumResultsForSearch: Infinity,
                    });
                    if (typeof options.initComplete == 'function') {
                        options.initComplete(settings, json);
                    }
                },
            };
            if (options.selectType) {
                dtOptions.select = {
                    style: 'multi',
                };
                dtOptions.order = [[1, 'asc']];
                options.columnsDef.unshift(
                    { target: 0, checkboxes: { selectRow: true, selectAllPages: true }, data: null, orderable: false, searchable: false, className: 'text-center vcenter', }
                );
                options.addActionColumn = false;
                options.showButtons = false;
                options.shortList = true;
            }
            if (options.addActionColumn) {
                options.columnsDef.push({
                    data: 'actions', name: 'actions',
                    orderable: false, searchable: false,
                    className: 'text-center hide-in-colvis', width: (options.buttonsInActionColumn*35) + 'px',
                });
            }
            if (options.dtOptions != null) {
                // Object.assign(dtOptions, options.dtOptions);
                mergeObject(dtOptions, options.dtOptions);
            }
            dtOptions.columns = options.columnsDef;
            if (options.showButtons) {
                dtOptions.buttons = [
                    {
                        extend: 'colvis',
                        text: '<i class="fa fa-th-list" data-toggle="tooltip" data-placement="bottom" data-title="Seleccionar columnas"></i>',
                        columns: ':not(.hide-in-colvis)',
                    },{
                        extend: 'excel',
                        text: '<i class="fa fa-file-excel-o text-green" data-toggle="tooltip" data-placement="bottom" data-title="Exportar a Excel"></i>',
                        exportOptions: { columns: ':visible:not(.hide-in-colvis)', },
                    },{
                        extend: 'pdf',
                        text: '<i class="fa fa-file-pdf-o text-red" data-toggle="tooltip" data-placement="bottom" data-title="Exportar a PDF"></i>',
                        exportOptions: {
                            columns: ':visible:not(.hide-in-colvis)',
                            format: {
                                body: function (data, row, column, node) {
                                    if (isHTML(data)) {
                                        var res = '';
                                        // Columna tipo Checkbox
                                        var isCheckbox = data.indexOf('<input') >= 0 && data.indexOf('type="checkbox"') >= 0;
                                        if (isCheckbox) {
                                            res = (data.indexOf('checked') >= 0 ? "√" : " ");
                                            return res;
                                        }
                                        // Otro tipo de checkbox
                                        if (data.indexOf('<p style=') == 0 && data.indexOf('fa-check') >= 0) {
                                            return "√";
                                        }
                                        // Columna de tags
                                        var isTags = data.indexOf('<span class="label label-success') >= 0;
                                        if (isTags) {
                                            $(data).each(function () {
                                                if ($(this).is('span')) {
                                                    res += $(this).text() + ', ';
                                                }
                                            });
                                            return res.slice(0, -2);
                                        }
                                        // Columna de texto
                                        var isTexto = data.indexOf('<') != 0;
                                        if (isTexto) {
                                            data = '<div>' + data + '</div>';
                                            res = $(data).text().trim();
                                            $(data).children().each(function() {
                                                // Tooltip
                                                var isTooltip = $(this).attr('data-toggle') == 'tooltip';
                                                if (isTooltip) {
                                                    var title = $(this).attr('title');
                                                    if (title == null) {
                                                        title = $(this).attr('data-title');
                                                    }
                                                    if (res.length > 0) {
                                                        res += ' - ';
                                                    }
                                                    res += title.replace(/(<([^>]+)>)/ig,"");
                                                }
                                            })
                                            return res;
                                        } else {
                                            // Tooltip
                                            var isTooltip = $(data).attr('data-toggle') == 'tooltip';
                                            if (isTooltip) {
                                                var title = $(data).attr('title');
                                                if (title == null) {
                                                    title = $(data).attr('data-title');
                                                }
                                                return title.replace(/(<([^>]+)>)/ig,"");
                                            }
                                        }
                                    }
                                    return data;
                                }
                            },
                        },
                        filename: '*',
                        download: 'open',
                        title: '',
                        customize: function (doc) {
                            // Alineación del texto de las columnas
                            var tbBody = doc.content[0].table.body;
                            var colHeaders = table.columns(':visible:not(.hide-in-colvis)').header();
                            colHeaders.each(function (th, colIdx, data) {
                                var alignment = null;
                                if ($(th).hasClass('text-center')) {
                                    alignment = 'center';
                                } else if ($(th).hasClass('text-right')) {
                                    alignment = 'right';
                                }
                                if (alignment != null) {
                                    tbBody.forEach(function (object, rowIdx, data) {
                                        var col = object[colIdx];
                                        col['alignment'] = alignment;
                                    });
                                }
                            });
                            // Título del listado
                            var title = $table.closest('.box').children('.box-header').first().children('h3').text();
                            if (! title) {
                                title = $('h1').first().text();
                            }
                            // Estilos por defecto
                            doc.defaultStyle.fontSize = 8;
                            doc.styles.tableHeader.fontSize = 9;
                            doc.styles.tableHeader.fillColor = '#3c8dbc';
                            doc.styles.tableHeader.alignment = 'left';
                            doc['pageMargins'] = [40, 60];
                            // Header del listado
                            doc['header'] = [{
                                table: {
                                    widths: ['*', '*'],
                                    body: [
                                        [
                                            { text: title.trim(), fontSize: 16, bold: true, },
                                            {
                                                alignment: 'right',
                                                text: [
                                                    { text: app_config.nombre_app, bold: true },
                                                    " (",
                                                    { text: app_config.nombre_corto },
                                                    ")"
                                                ],
                                                fontSize: 14,
                                                color: '#666',
                                            }
                                        ],
                                        [" ", " "],
                                    ],
                                },
                                layout: {
                                    hLineWidth: function(i, node) {
                                        return (i === 0 || i === node.table.body.length) ? 0 : 0.2;
                                    },
                                    vLineWidth: function(i, node) { return 0; },
                                    hLineColor: function(i, node) { return '#c3c3c3'},
                                },
                                margin: [40, 30],
                            }];
                            // Footer del listado
                            doc['footer'] = (function(page, pages) {
                                return {
                                    table: {
                                        widths: ['*', '*', '*'],
                                        body: [
                                            [
                                                { text: window.location.href, italics: true, fontSize: 8, color: '#888', margin: [0, 5], },
                                                {
                                                    alignment: 'center',
                                                    text: [
                                                        { text: page.toString(), italics: true },
                                                        ' de ',
                                                        { text: pages.toString(), italics: true }
                                                    ],
                                                    color: '#888',
                                                    margin: [0, 5],
                                                },
                                                {
                                                    alignment: 'right',
                                                    image: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGsAAAAwCAYAAAAW9oQ4AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyRpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoTWFjaW50b3NoKSIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDpGMzEwQkQ2NjI5NkMxMUU2QTMwNEE1RDcyMkNCMzQ4OCIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDpGMzEwQkQ2NzI5NkMxMUU2QTMwNEE1RDcyMkNCMzQ4OCI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOkI2QUY4NjYxMjk1RTExRTZBMzA0QTVENzIyQ0IzNDg4IiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOkI2QUY4NjYyMjk1RTExRTZBMzA0QTVENzIyQ0IzNDg4Ii8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpSREY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+1rYlWgAAEHVJREFUeNrsWwt0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',
                                                    fit: [55, 55],
                                                },
                                            ],
                                            [' ', ' ', ' ']
                                        ]
                                    },
                                    layout: {
                                        hLineWidth: function(i, node) {
                                            return (i === 0) ? 0.1 : 0;
                                        },
                                        vLineWidth: function(i, node) { return 0; },
                                        hLineColor: function(i, node) { return '#c3c3c3'},
                                    },
                                    margin: [40, 20]
                                }
                            });
                        }
                    },
                ];
            }
            if (options.shortList) {
                dtOptions.pageLength = app_config.filas_tablas_modal;
                dtOptions.stateSave = false;
            }
            var table = $('#' + options.tableName).DataTable(dtOptions);
            if (options.urlEdit != null) {
                $('#' + options.tableName + ' tbody').on('click', 'tr', function () {
                    var data = table.row(this).data();
                    if (showArchive) {

                    } else {
                        window.location.href = options.urlEdit.replace('XX', data.id);
                    }
                });
            }
            // Archivo
            var showArchive = false;
            var $tableTitle = $('button.archive-button').closest('.box-header').children('.box-title');
            var tableTitleText = $tableTitle.text();
            $('button.archive-button').on('click', function () {
                showArchive = !showArchive;
                if (showArchive) {
                    $tableTitle.text(tableTitleText + ' (Archivados)');
                    $tableTitle.addClass('text-red');
                    table.ajax.url(options.urlListArchive).load();
                    $table.on('click', 'button.restore-button', function(e) {
                        e.stopPropagation();
                        var row = $(e.currentTarget);
                        bootbox.dialog({
                            title: "Por favor, confirme",
                            message: "<h3>¿Está seguro de querer restaurar el registro?</h3>",
                            className: "modal-success",
                            onEscape: function() {},
                            buttons: {
                                si: {
                                    label: "Sí, quiero recuperarlo",
                                    className: "btn-outline pull-right",
                                    callback: function() {
                                        var route = window.location.pathname + "/restore/" + row.data('id');
                                        $.post(route, function(d) {
                                            if (d != null) {
                                                if (d.result != null) {
                                                    if (d.result == 'success') {
                                                        location.reload();
                                                        return true;
                                                    }
                                                }
                                            }
                                            bootbox.alert("Ha ocurrido un error y no se ha podido restaurar.");
                                        });
                                    }
                                },
                                no: {
                                    label: "Cancelar",
                                    className: "btn-outline pull-left",
                                },
                            }
                        });
                    })
                } else {
                    $tableTitle.text(tableTitleText);
                    $tableTitle.removeClass('text-red');
                    table.ajax.url(options.urlList).load();
                }
            });
            return table;
        },
        // DIALOGO CONFIRMACION QUITAR DOCUMENTO
        setDeleteDialog: function(item_name, message) {
            function removeConfirmationDialog(yes_callback) {
                var msg = "<h3>¿Está seguro de querer archivar el " + item_name + "?</h3>";
                if (message == null) {
                    msg += "(No se eliminará definitivamente, sino que se almacenará en el <em>Histórico</em>)";
                } else {
                    msg += message;
                }
                bootbox.dialog({
                    title: "Por favor, confirme",
                    message: msg,
                    className: "modal-danger",
                    onEscape: function() {},
                    buttons: {
                        si: {
                            label: "Sí, quiero archivarlo",
                            className: "btn-outline pull-right",
                            callback: yes_callback
                        },
                        no: {
                            label: "Cancelar",
                            className: "btn-outline pull-left",
                        },
                    }
                });
            }
            var table = $.fn.dataTable.fnTables(true);
            if (table.length > 0) {
                var $table_body = $("#" + $(table).attr('id') + " tbody");
                $table_body.on('click', 'button.delete-button', function (e) {
                    e.stopPropagation();
                    var a = $(e.currentTarget);
                    var id = a.data('id');
                    removeConfirmationDialog(function() {
                        var route = window.location.pathname + "/remove/" + id;
                        $.post(route, function(d) {
                            if (d != null) {
                                if (d.result != null) {
                                    if (d.result == 'success') {
                                        location.reload();
                                        return true;
                                    }
                                }
                            }
                            bootbox.alert("Ha ocurrido un error y no se ha podido archivar.")
                        });
                    });
                });
            }
        },
        getSelectedRowsIds: function(tableName) {
            var t = $('#' + tableName + ' > tbody > tr.selected');
            var ids = '';
            $.each(t, function(index, row) {
                ids += row.id + ',';
            });
            return ids.slice(0,-1);
        },
        addSelectedRowsInput: function(tableName, $form, fieldName) {
            if (fieldName == null) {
                fieldName = 'ids';
            }
            var ids = $.App.DT.getSelectedRowsIds(tableName);
            $('input[name="' + fieldName + '"]').remove();
            $form.append(
                $('<input>')
                    .attr('type', 'hidden')
                    .attr('name', fieldName)
                    .val(ids)
            );
        }
    };
    // BOXES
    $.App.Box = {
        init: function(boxId, title, color, border) {
            if (color == null) {
                color = 'primary';
            }
            if (border == null) {
                border = true;
            }
            var $box = $('#' + boxId);
            if (border) {
                $box.addClass('box box-' + color);
            }
            var $bHeader = $('<div>', { 'class': 'box-header' });
            $box.append($bHeader);
            var $bTitle = $('<h3>', {
                'class': 'box-title',
                html: title
            });
            $bHeader.append($bTitle);
            var $bBody = $('<div>', { 'class': 'box-body' });
            $box.append($bBody);
            var $table = $('<table>', {
                id: boxId + '-table',
                'class': 'table table-bordered table-hover',
            }).append($('<thead>').append($('<tr>')));
            $table.append($('<tbody>'));
            $bBody.append($table);
            return $box;
        },
        addTD: function(boxId, attributes) {
            var $box = $('#' + boxId);
            var $tr = $box.find('table').find('thead').find('tr');
            $tr.append($('<td>', attributes));
        },
        addActionButton: function(boxId, target, data, buttonTitle, buttonIcon) {
            if (buttonTitle == null) {
                buttonTitle = 'Añadir';
            }
            if (buttonIcon == null) {
                buttonIcon = 'plus';
            }
            var options = {
                type: 'button',
                'class': 'btn btn-primary bootstrap-modal-form-open',
                html: '<i class="fa fa-' + buttonIcon + '"></i> &nbsp;' + buttonTitle
            }
            if (target != null) {
                $.extend(options, {
                    'data-toggle': 'modal',
                    'data-target': '#' + target,
                });
            }
            if (data != null) {
                $.extend(options, data);
            }
            var $bHead = $('#' + boxId + ' .box-header');
            $bHead.append(
                $('<div>', {'class': 'box-tools'}).append(
                    $('<button>', options)
                )
            );
        },
    };
    // SELECTIZE
    $.App.Selectize = {
        init: function(tag_list, tags_selector) {
            if (typeof(tag_list) === 'undefined') tag_list = '';
            if (typeof(tags_selector) === 'undefined') tags_selector = "#tags";
            var $tags = $(tags_selector);
            if ($tags != null) {
                $.getScript('/plugins/selectize/selectize.min.js', function () {
                    $tags.selectize({
                        plugins: ['remove_button'],
                        delimiter: ',',
                        persist: false,
                        valueField: 'tag',
                        labelField: 'tag',
                        searchField: 'tag',
                        options: tags,
                        create: function(input) {
                            return {
                                tag: input
                            }
                        }
                    });
                    var tags = tag_list.split(',');
                    console.log(tags);
                });
            } else {
                console.log("App.Selectize: '" + tags_selector + "' not found!");
            }
        },
    };
    // Notificaciones
    $.App.notify = {
        success: function(msg) {
            $.notify({
                title: '<strong>OK</strong>',
                message: msg,
                icon: 'icon fa fa-lg fa-check',
            },{
                type: 'success',
            });
        },
        error: function(msg) {
            $.notify({
                title: '<strong>¡ERROR!</strong>',
                message: msg,
                icon: 'icon fa fa-lg fa-times',
            },{
                type: 'danger',
            });
        },
    };

}
