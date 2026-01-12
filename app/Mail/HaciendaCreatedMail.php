<?php

namespace App\Mail;

use App\Models\Hacienda;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class HaciendaCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public Hacienda $hacienda;

    public function __construct(Hacienda $hacienda)
    {
        $this->hacienda = $hacienda->load(['categoria','establecimiento']);
    }

    public function build()
    {
        return $this->subject('Nueva Hacienda cargada #'.$this->hacienda->id)
                    ->markdown('emails.haciendas.created', [
                        'hacienda' => $this->hacienda,
                        'subtotal' => $this->hacienda->subtotal_peso_vivo,
                    ]);
    }
}
