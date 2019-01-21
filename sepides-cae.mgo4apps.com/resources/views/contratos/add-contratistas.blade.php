<div class="modal fade" id="add-contratista-wizard" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-contratista-wizard-title">Añadir Contratista - Paso 1/2</h4>
            </div>
            <form id="add-contratista-wizard-form" class="form-horizontal" method="POST" action="{{ route('contratos.addContratista') }}">
                <input type="hidden" name="contratista_id" id="contratista_id" />
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="add-contratista-wizard-errors-box" class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i> ¡Vaya! Hay algunos problemas con su entrada.</h4>
                                <ul></ul>
                            </div>
                        </div>
                    </div>
                    <div id="add-contratista-wizard-page-1">
                        <div class="form-group">
                            <div class="col-sm-1">
                                <input type="radio" name="tipo_contratista" id="contratista_existente" class="form-control" checked>
                            </div>
                            <label for="contratista_existente" class="form-label col-sm-10">Contratista existente</label>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-1">&nbsp;</div>
                            <div class="col-sm-10">
                                {{ Form::select('contratista_seleccionado', [], null, ['class' => 'form-control', 'id' => 'contratista_seleccionado', 'placeholder' => 'Seleccione empresa...']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-1">
                                <input type="radio" name="tipo_contratista" id="contratista_nuevo" class="form-control">
                            </div>
                            <label for="contratista_nuevo" class="form-label col-sm-10">Crear nuevo Contratista</label>
                        </div>
                    </div>
                    <div id="add-contratista-wizard-page-2a">
                        <h5>Datos del nuevo contratista</h5>
                        {{ Form::fhText('razon_social', 'Razón Social', null, true) }}
                        {{ Form::fhText('cif', 'CIF/NIF', null, true, 6) }}
                        <hr />
                        <div id="persona-contacto-group">
                            <h5>Persona de contacto</h5>
                            {{ Form::fhText('nombre', null, null, true, 6) }}
                            {{ Form::fhText('apellidos', null, null, true) }}
                            {{ Form::fhText('nif', 'NIF/DNI', null, true, 4) }}
                            {{ Form::fhText('puesto', null, null, true) }}
                            {{ Form::fhEmail('email', null, true) }}
                            <hr />
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h4><i class="icon fa fa-info"></i> Al pulsar el botón 'Finalizar'...</h4>
                                    <ul>
                                        <li>... se creará una nueva <strong>Empresa</strong> con el <em>CIF/NIF</em> y la <em>Razón Social</em> introducidos.</li>
                                        <li id="2a_li_2">... se asociará la <em>Empresa</em> creada como <strong>Contratista</strong> del contrato.</li>
                                        <li id="2a_li_3">... se creará un nuevo <strong>Trabajador</strong> para la <em>empresa contratista</em> con los datos introducidos.</li>
                                        <li id="2a_li_4">... se creará un nuevo <strong>Usuario</strong> en la plataforma y se asociará al <strong>Trabajador</strong>.</li>
                                        <li id="2a_li_5">... se enviará un email al <strong>Usuario</strong> para que se una a la plataforma.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="add-contratista-wizard-page-2b">
                        <h5>Persona de contacto del contratista</h5>
                        <div class="form-group required">
                            <div class="col-md-12">
                                <select id="persona_contacto" name="persona_contacto" class="form-control"></select>
                            </div>
                        </div>
                        <hr />
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h4><i class="icon fa fa-info"></i> Al pulsar el botón 'Finalizar'...</h4>
                                    <ul>
                                        <li id="2b_li_1">... se asociará la <em>Empresa</em> elegida como <strong>Contratista</strong> del contrato.</li>
                                        <li id="2b_li_2">... se enviará un email al <strong>Usuario</strong>) para informarle.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id="add-contratista-wizard-next-button" type="button" class="btn ">Siguiente &nbsp; <i class="fa fa-arrow-right"></i></button>
                <button id="add-contratista-wizard-back-button" type="button" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Atrás</button>
                <button id="add-contratista-wizard-final-button" type="submit" class="btn btn-success"><i class="fa fa-check"></i> Finalizar</button>
            </div>
        </div>
    </div>
</div>
