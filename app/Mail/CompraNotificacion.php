<?php

namespace App\Mail;

use App\Models\Compra;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompraNotificacion extends Mailable
{
  use Queueable, SerializesModels;

  public function __construct(
    public Compra $compra,
    public string $action = 'creada'
  ) {}

  public function build()
  {
    return $this->subject("Compra {$this->action} #{$this->compra->codigo}")
      ->markdown('emails.compras.notificacion');
  }
}
