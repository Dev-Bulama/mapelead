<?php

namespace App\Http\Controllers\Admin;

use App\Models\AdmissionNumberSetting;
use App\Models\User;
use Illuminate\Http\Request;

class AdmissionNumberController extends \App\Http\Controllers\Controller
{
    public function settings()
    {
        $settings = AdmissionNumberSetting::instance();

        return view('admin.settings.admission-numbers', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'prefix'              => 'required|max:10',
            'separator'           => 'required|max:5',
            'include_year'        => 'boolean',
            'include_course_code' => 'boolean',
            'digit_length'        => 'integer|min:2|max:8',
            'reset_yearly'        => 'boolean',
        ]);

        $settings = AdmissionNumberSetting::instance();
        $settings->update($validated);

        return redirect()->back()->with('success', 'Admission number settings saved.');
    }

    public function assign(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
        ]);

        $user = User::findOrFail($request->user_id);

        $number = AdmissionNumberSetting::instance()->generateNumber();

        $user->update(['admission_number' => $number]);

        return redirect()->back()->with('success', "Admission number {$number} assigned to {$user->full_name}.");
    }

    public function bulkAssign()
    {
        $students = User::role('student')
            ->whereNull('admission_number')
            ->get();

        $count = 0;

        foreach ($students as $student) {
            $number = AdmissionNumberSetting::instance()->generateNumber();
            $student->update(['admission_number' => $number]);
            $count++;
        }

        return redirect()->back()->with('success', "Assigned {$count} admission numbers.");
    }

    public function index()
    {
        $users = User::whereNotNull('admission_number')->paginate(20);

        return view('admin.settings.admission-numbers-list', compact('users'));
    }
}
