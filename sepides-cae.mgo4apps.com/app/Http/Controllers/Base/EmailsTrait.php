<?php
namespace App\Http\Controllers\Base;

use Mail;
use Auth;
use Illuminate\Mail\TransportManager;

use Snowfire\Beautymail\Beautymail;

use App\Models\Empresa;
use App\Models\Provincia;
use App\User;

trait EmailsTrait
{

    // Email para el reseteo de la contraseña de los usuarios
    public function sendResetUserPasswordEmail($user, $token = null)
    {
        if ($token == null) {
            $token = $this->getResetUserToken($user);
        }

        $this->__sendMail('reset-password', $user, 'Necesitas crear una nueva contraseña', compact('token'));
    }

    // Email para invitar a la persona de contacto de un contratista a un contrato
    public function sendInvitacionContratistaContratoEmail($nuevo, $trabajador, $contrato)
    {
        // Trabajador
        $trabajador_nombre = $trabajador->nombre;

        // Usuario
        $usuario = $trabajador->user()->get()->first();
        if ($nuevo) {
            // Reset token
            $token = $this->getResetUserToken($usuario);
        }

        // Contrato
        $contrato_nombre = $contrato->nombre;
        $contrato_referencia = $contrato->referencia;

        $template = $nuevo ? 'invitacion-cae' : 'invitacion-contrato';
        $subject = $nuevo ? 'Invitación a la plataforma $nombre_plataforma' : 'Invitación para participar en un nuevo contrato';

        $this->__sendMail($template, $usuario, $subject, compact('trabajador_nombre', 'contrato_nombre', 'contrato_referencia', 'token'));
    }

    // Email para contactar con soporte
    public function sendSoporteEmail($respuesta_soporte, $email_respuesta, $asunto, $mensaje, $usuario_nombre = null)
    {
        $usuario = new User();

        if ($respuesta_soporte == true) {
            $usuario->email = $email_respuesta;
            $usuario->nombre = $usuario_nombre;

            $this->__sendMail('soporte-mensaje-respuesta', $usuario, '$nombre_plataforma: Respuesta a su solicitud de soporte', compact('asunto', 'mensaje'));
        } else {
            $usuario->email = 'daniel@addtime.es';
            $usuario->nombre = 'Soporte CAE';

            $usuario_nombre = Auth::user()->nombre;
            $usuario_id = Auth::user()->id;

            $this->__sendMail('soporte-mensaje', $usuario, '$nombre_plataforma: Nueva solicitud de soporte', compact('usuario_nombre', 'usuario_id', 'email_respuesta', 'asunto', 'mensaje'));
        }
    }




    //**************************************************************************

    // Función que envía los emails (pone datos necesarios para todos los emails)
    private function __sendMail($template, $usuario, $subject, $data = null)
    {
        $empresa_principal = Empresa::findOrFail(0);
        $empresa_principal_nombre = config('cae.nombre_corto');
        $empresa_principal_razon_social = $empresa_principal->razon_social;

        $nombre_plataforma = config('app.name') . ' (' . $empresa_principal_nombre . ')';
        $subject = str_replace('$nombre_plataforma', $nombre_plataforma, $subject);
        $footer_text = $this->__footerText($empresa_principal, $nombre_plataforma);

        $_data = compact('usuario', 'empresa_principal_nombre', 'empresa_principal_razon_social', 'nombre_plataforma', 'subject', 'footer_text');

        if ($data) {
            $_data = array_merge($_data, $data);
        }

        // Fix para el error en Nginx con los dominios con *
        $this->fixNginxWildcardSwiftMailerError();

        $bmail = app()->make(\Snowfire\Beautymail\Beautymail::class);
        $bmail->send('emails.' . $template, $_data, function ($m) use ($nombre_plataforma, $usuario, $subject) {
            $m->from(config('mail.host') == 'smtp.mailtrap.io' ? 'apps.services@mgo.es' : config('mail.username'), $nombre_plataforma);
            $m->to($usuario->email, $usuario->nombre);
            $m->subject($subject);
        });
    }

    private function __footerText($empresa_principal, $nombre_plataforma)
    {
        $provincia_str = '';
        $provincia = Provincia::find($empresa_principal->provincia_id);
        if ($provincia) {
            if (strtolower($provincia->nombre) != strtolower($empresa_principal->municipio)) {
                $provincia_str = ' (' . $provincia->nombre . ')';
            }
        }

        return '<p>Por favor no responda a esta dirección de email. Este email ha sido enviado de forma automática por la plataforma ' . $nombre_plataforma . '. Los emails enviados a esta dirección no serán respondidos.</p>' .
            //    '<p>La Informacion incluida en el presente correo electrónico es SECRETO PROFESIONAL Y CONFIDENCIAL, siendo para el uso exclusivo del destinatario arriba mencionado.<br/>' .
            //    'Si usted no es el destinatario del mensaje o ha recibido esta comunicacion por error le informamos que está totalmente prohibida cualquier divulgación, distribución o reproducción de esta comunicación.<br/>' .
            //    'Le rogamos que nos lo notifique inmediatamente y nos devuelva el mensaje original a la dirección arriba mencionada.</p>' .
            (file_exists(public_path('img/logo-empresa.png')) ? '<img src="' . route('home') . '/img/logo-empresa-small.png" />' : '') .
            '<p><small>' . $empresa_principal->razon_social . '<br/>' . $empresa_principal->direccion . '<br/>' . $empresa_principal->codigo_postal . ' - ' . $empresa_principal->municipio . $provincia_str .
            '</small></p>';
    }

    // Crea el 'reset token' en la tabla 'pasword_resets'
    // Devuelve el token como una cadena
    private function getResetUserToken($user)
    {
        return app('auth.password.broker')->createToken($user);
        ;
    }

    private function fixNginxWildcardSwiftMailerError()
    {
        // Hay un problema con el SwiftMailer en configuraciones Nginx con los
        // dominios/subdominios con '*'.
        // Google Mail no los admite en el HELO y da error. Así que la solución
        // es forzar que SwiftMailer siempre mande '127.0.0.1' como dominio local
        app()['mailer']->getSwiftMailer()->getTransport()->setLocalDomain('[127.0.0.1]');
    }
}
