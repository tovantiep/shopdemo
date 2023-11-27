<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderApproved extends Mailable
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
     * @return OrderApproved
     */
    public function build(): OrderApproved
    {
        return $this->view('emails.order.approved')->with(['data' => $this->data]);
    }

}
