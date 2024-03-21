<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class LocalAppointmentConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The PDF data.
     *
     * @var string
     */
    public $pdfData;

    /**
     * Create a new message instance.
     *
     * @param string $pdfData
     */
    public function __construct($pdfData)
    {
        $this->pdfData = $pdfData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Local Appointment Confirmation')
                    ->view('emails.local-appointment')
                    ->attachData($this->pdfData, 'ticket.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
