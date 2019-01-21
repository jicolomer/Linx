<div class="modal fade" id="add-documento-privado-dialog" tabindex="-1" role="dialog" aria-labelledby="wizardLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Añadir Documento privado al contrato</h4>
            </div>
            {!! Form::open(['id' => 'add-documento-privado-form', 'route' => 'contratos.addDocumentoPrivado', 'class' => 'form-horizontal', 'files' => true]) !!}
                <div class="modal-body">
   					{{ Form::fhText('add-documento-privado-nombre', 'Nombre') }}
                    {{ Form::fhFile('add-documento-privado-file', 'Fichero', 'Seleccione', true, 8, ['accept' => '.jpg, .jpeg, .png, .gif, .pdf, image/jpeg, image/png, image/gif, application/pdf']) }}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
