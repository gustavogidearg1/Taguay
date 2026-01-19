<?php

namespace App\Mail;

use App\Models\Contrato;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContratoNotificacion extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contrato $contrato,
        public string $accion // 'creado' | 'actualizado'
    ) {}

    public function build()
    {
        $subject = $this->accion === 'creado'
            ? "Contrato creado #{$this->contrato->nro_contrato}"
            : "Contrato actualizado #{$this->contrato->nro_contrato}";

        return $this->subject($subject)
            ->view('emails.contratos.notificacion');
    }
}
