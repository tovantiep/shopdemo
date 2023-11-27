<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderShip extends Mailable
{
    use Queueable, SerializesModels;

    private mixed $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Get the message envelope.
     *
     * @return OrderShip
     */
    public function build(): OrderShip
    {
        return $this->view('emails.order.ship')->with(['data' => $this->data]);
    }

}
