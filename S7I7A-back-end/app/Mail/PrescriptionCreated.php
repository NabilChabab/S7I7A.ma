<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PrescriptionCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $pdfContents;

    public function __construct($pdfContents)
    {
        $this->pdfContents = $pdfContents;
    }

    public function build()
    {
        return $this->view('emails.prescription_created')
                    ->attachData($this->pdfContents, 'prescription.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
