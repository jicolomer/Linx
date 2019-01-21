<div class="modal fade" id="add-trabajadores-maquinas-wizard" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="add-trabajadores-maquinas-wizard-title">Añadir Trabajadores - Paso 1/3</h4>
            </div>
            <form id="add-trabajadores-maquinas-wizard-form" class="form-horizontal">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="add-trabajadores-maquinas-wizard-errors-box" class="alert alert-danger alert-dismissible" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-ban"></i> ¡Vaya! Hay algunos problemas con su entrada.</h4>
                                <ul></ul>
                            </div>
                        </div>
                    </div>
                    <div id="add-trabajadores-maquinas-wizard-page-1">
                        <div id="add-trabajadores-maquinas-wizard-trabajadores-table">
                            <h4>Trabajadores</h4>
                            <table id="select-trabajadores-table">
        						<thead>
        							<tr>
        								<th class="text-center" width="10px"><input id="trabajadores-select-all" type="checkbox" class="form-control minimal"></th>
                                        <th class="text-right">ID</th>
                                        <th data-priority="1">Apellidos</th>
                                        <th data-priority="2">Nombre</th>
                                        <th data-priority="3">NIF/DNI</th>
                                        <th>Puesto de trabajo</th>
                                        <th data-priority="4">¿Formación?</th>
                                        <th data-priority="5">¿Información?</th>
                                        <th data-priority="4">¿EPIS?</th>
                                        <th data-priority="5">¿Vigilancia Salud?</th>
                                        <th data-priority="6">¿Otros?</th>
        							</tr>
        						</thead>
        						<tbody></tbody>
        					</table>
                        </div>
                        <div id="add-trabajadores-maquinas-wizard-maquinas-table">
                            <h4>Máquinas</h4>
                            <table id="select-maquinas-table">
        						<thead>
        							<tr>
        								<th class="text-center" width="10px"><input id="maquinas-select-all" type="checkbox" class="form-control minimal"></th>
                                        <th class="text-right">ID</th>
										<th data-priority="4">Tipo</th>
										<th data-priority="1">Nombre</th>
										<th data-priority="2">Matrícula</th>
										<th>Marca</th>
										<th>Modelo</th>
										<th>Documentación</th>
        							</tr>
        						</thead>
        						<tbody></tbody>
        					</table>
                        </div>
                    </div>
                    <div id="add-trabajadores-maquinas-wizard-page-2">
                        <h4>Centros de trabajo del contrato</h4>
                        <table id="select-centros-table">
    						<thead>
    							<tr>
    								<th class="text-center" width="10px"><input id="centros-select-all" type="checkbox" class="form-control minimal"></th>
    								<th class="text-right" width="50px">ID</th>
    								<th>Nombre</th>
    								<th>Código Postal</th>
    								<th>Municipio</th>
    							</tr>
    						</thead>
    						<tbody></tbody>
    					</table>
                    </div>
                    <div id="add-trabajadores-maquinas-wizard-page-3">
                        <h4>Fechas de trabajo</h4>
                        {{ Form::fhDate('fecha_inicio', null, true) }}
                        {{ Form::fhDate('fecha_final', null, true) }}
                        <hr />
                        <h4>Días de trabajo</h4>
                        <br />
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="col-xs-3 no-padding">
                                    <button type="button" id="entre_semana_button" class="btn btn-block btn-flat btn-success">Entre semana</button>
                                </div>
                                <div class="col-xs-3 no-padding">
                                    <button type="button" id="finde_semana_button" class="btn btn-block btn-flat btn-danger">Fines de semana</button>
                                </div>
                                <div class="col-xs-3 no-padding">
                                    <button type="button" id="toda_semana_button" class="btn btn-block btn-flat btn-primary">Todos los días</button>
                                </div>
                                <div class="col-xs-3 no-padding">
                                    <button type="button" id="limpiar_semana_button" class="btn btn-block btn-flat btn-default">Limpiar</button>
                                </div>
                            </div>
                        </div>
                        <br />
                        <div class="row">
                            <div class="col-xs-12 seven-cols">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="text-center col-md-1">Lunes</th>
                                            <th class="text-center col-md-1">Martes</th>
                                            <th class="text-center col-md-1">Miércoles</th>
                                            <th class="text-center col-md-1">Jueves</th>
                                            <th class="text-center col-md-1">Viernes</th>
                                            <th class="text-center col-md-1">Sábado</th>
                                            <th class="text-center col-md-1">Domingo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="text-center"><input type="checkbox" id="lunes" name="lunes"></td>
                                            <td class="text-center"><input type="checkbox" id="martes" name="martes"></td>
                                            <td class="text-center"><input type="checkbox" id="miercoles" name="miercoles"></td>
                                            <td class="text-center"><input type="checkbox" id="jueves" name="jueves"></td>
                                            <td class="text-center"><input type="checkbox" id="viernes" name="viernes"></td>
                                            <td class="text-center"><input type="checkbox" id="sabado" name="sabado"></td>
                                            <td class="text-center"><input type="checkbox" id="domingo" name="domingo"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="modal-footer">
                <button id="add-trabajadores-maquinas-wizard-back-button" type="button" class="btn btn-primary pull-left"><i class="fa fa-arrow-left"></i> Atrás</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button id="add-trabajadores-maquinas-wizard-next-button" type="button" class="btn ">Siguiente &nbsp; <i class="fa fa-arrow-right"></i></button>
                <button id="add-trabajadores-maquinas-wizard-final-button" type="submit" class="btn btn-success"><i class="fa fa-check"></i> Finalizar</button>
            </div>
        </div>
    </div>
</div>
