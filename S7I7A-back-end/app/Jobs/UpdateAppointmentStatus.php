<?php

namespace App\Jobs;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAppointmentStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $now = Carbon::now();

        $appointmentsInProgress = Appointment::where('status', 'pending')
            ->where('appointment_date', '<=', $now->format('Y-m-d'))
            ->where('appointment_hour', '<=', $now->format('H:i'))
            ->get();

        foreach ($appointmentsInProgress as $appointment) {
            $appointment->status = 'inProgress';
            $appointment->save();
        }

        $completedAppointments = Appointment::where('status', 'inProgress')
            ->where(function ($query) use ($now) {
                $query->where('appointment_date', '<', $now->format('Y-m-d'))
                    ->orWhere(function ($query) use ($now) {
                        $query->where('appointment_date', $now->format('Y-m-d'))
                            ->where('appointment_hour', '<', $now->format('H:i'));
                    });
            })
            ->get();

        foreach ($completedAppointments as $appointment) {
            $appointment->status = 'completed';
            $appointment->save();
        }
    }
}
