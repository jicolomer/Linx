                </div>
                <div class="box-footer">
                    <button type="submit" class="btn btn-primary">{{ $submitBtn }}</button>
                    @if($cancelBtnRoute)
                    <a href="{{ (substr($cancelBtnRoute,0,7) === "http://") ? $cancelBtnRoute : route($cancelBtnRoute) }}" class= "btn btn-default">Cancelar</a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
