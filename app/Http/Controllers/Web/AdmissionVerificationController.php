<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\User;

class AdmissionVerificationController extends Controller
{
    public function verify(string $number)
    {
        $user = User::whereRaw('UPPER(admission_number) = ?', [strtoupper($number)])
            ->with([
                'enrollments.course',
                'enrollments.batch.batch',
            ])
            ->first();

        if (!$user) {
            return view('web.admission-verify', [
                'notFound' => true,
                'number'   => $number,
            ]);
        }

        $enrollment = $user->enrollments
            ->where('status', 'active')
            ->sortByDesc('enrolled_at')
            ->first()
            ?? $user->enrollments->sortByDesc('enrolled_at')->first();

        return view('web.admission-verify', compact('user', 'enrollment') + ['notFound' => false]);
    }
}
