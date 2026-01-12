<?php

namespace App\Mail;

use App\Models\Lluvia;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LluviaCreadaMail extends Mailable
{
    use Queueable, SerializesModels;

    public Lluvia $lluvia;

    public function __construct(Lluvia $lluvia)
    {
        $this->lluvia = $lluvia->load(['establecimiento','user']);
    }

    public function build()
    {
        $fecha = optional($this->lluvia->fecha)->format('d/m/Y');
        $hora  = $this->lluvia->hora ? $this->lluvia->hora->format('H:i') : '—';

        return $this->subject("Nueva lluvia {$this->lluvia->mm} mm · {$fecha} {$hora}")
            ->markdown('emails.lluvias.created', [
                'lluvia' => $this->lluvia,
                'fecha'  => $fecha,
                'hora'   => $hora,
            ]);
    }
}
