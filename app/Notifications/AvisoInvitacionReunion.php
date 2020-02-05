<?php

namespace App\Notifications;

use App\Reunion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AvisoInvitacionReunion extends Notification
{
    use Queueable;

    protected $reunion;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Reunion $reunion)
    {
        $this->reunion = $reunion;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notificado
     * @return array
     */
    public function via($notificado)
    {
        $preferencias = [];
        if($notificado->prefiere_email) array_push($preferencias, 'mail');
        if($notificado->prefiere_notificaciones_app) array_push($preferencias, 'database');
        return  $preferencias;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notificado
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notificado)
    {
        $link_reunion = route('reuniones.show', $this->reunion->id);
        $link_ordendeldia = route('reuniones.ordendeldia.descargar', $this->reunion->id);
        return (new MailMessage)
                    ->subject("Invitación a Reunión")
                    ->greeting("Hola, {$notificado->gradoNombreCompleto}.")
                    ->line("Fuiste convocado la reunión que se celebrará el día "
                    . "**" . $this->reunion->inicio->formatLocalized('%A, %d de %B de %Y') . "**"
                    . " por parte de la Academia de {$this->reunion->academia->nombre}"
                    . " de la {$this->reunion->academia->departamento->division->nombre}.")
                    ->line("- **Lugar:** {$this->reunion->lugar}")
                    ->line("- **Hora de inicio:** " . $this->reunion->inicio->format('h:i A'))
                    ->line("- **Hora de finalización:** " . $this->reunion->fin->format('h:i A'))
                    ->action("Descargar orden del día", $link_ordendeldia)
                    ->line("Para más detalles, consulta el siguiente link: {$link_reunion}.");
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notificado
     * @return array
     */
    public function toArray($notificado)
    {
        return [
            //
        ];
    }
}