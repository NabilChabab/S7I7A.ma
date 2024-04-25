<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\LocalAppointmentConfirmation;

class SendAppointmentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $pdf;

    public function __construct($email, $pdf)
    {
        $this->email = $email;
        $this->pdf = $pdf;
    }

    public function handle()
    {
        Mail::to($this->email)->send(new LocalAppointmentConfirmation($this->pdf));
    }
}
?>
