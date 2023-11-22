<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCancel extends Mailable
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
     * @return OrderCancel
     */
    public function build(): OrderCancel
    {
        return $this->view('emails.order.cancel')->with(['data' => $this->data]);
    }

}
