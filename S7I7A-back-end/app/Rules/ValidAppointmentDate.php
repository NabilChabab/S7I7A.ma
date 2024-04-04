<?php

namespace App\Rules;

use App\Models\Appointment;
use Carbon\Carbon;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ValidAppointmentDate implements ValidationRule
{

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */

    public function validate(string $attribute, mixed $value, Closure $fail) : void
    {
        $appointmentDateTime = Carbon::parse($value['appointment_date'] . ' ' . $value['appointment_hour']);

        $existingAppointment = Appointment::where('user_id', Auth::id())
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->exists();

        $isAvailable = Appointment::where('doctor_id', $value['doctor_id'])
            ->where('appointment_date', $appointmentDateTime->format('Y-m-d'))
            ->where('appointment_hour', $appointmentDateTime->format('H:i'))
            ->doesntExist();

        if ($existingAppointment) {
            throw ValidationException::withMessages([
                'appointment' => ['You already have an appointment at this time.'],
            ]);
        } elseif (!$isAvailable) {
            throw ValidationException::withMessages([
                'appointment' => ['Doctor is not available at this time. Please choose another time.'],
            ]);
        }

    }

    public function message()
    {
        return 'The selected appointment date and time is not available.';
    }
}
