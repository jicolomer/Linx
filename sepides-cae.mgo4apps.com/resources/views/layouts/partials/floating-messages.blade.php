@if (Session::has('flash_notification.message'))
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-{{ Session::get('flash_notification.level') }} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            @if (Session::get('flash_notification.level') == 'info')
                <h4><i class="icon fa fa-info"></i> Info</h4>
            @elseif (Session::get('flash_notification.level') == 'warning')
                <h4><i class="icon fa fa-warning"></i> ¡Atención!</h4>
            @elseif (Session::get('flash_notification.level') == 'danger')
                <h4><i class="icon fa fa-ban"></i> ¡Error!</h4>
            @elseif (Session::get('flash_notification.level') == 'success')
                <h4><i class="icon fa fa-check"></i> OK</h4>
            @endif
                <p>{!! Session::get('flash_notification.message') !!}</p>
            </div>
        </div>
    </div>
@elseif(isset($errors) && $errors->any())
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="alert alert-danger alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> ¡Vaya! Hay algunos problemas con su entrada.</h4>
                <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif
