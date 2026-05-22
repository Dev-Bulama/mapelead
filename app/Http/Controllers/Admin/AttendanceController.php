<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceSession;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSettings;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\LMS\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Course $course)
    {
        $sessions = AttendanceSession::where('course_id', $course->id)
            ->paginate(20);

        return view('admin.lms.attendance.index', compact('course', 'sessions'));
    }

    public function createSession(Course $course)
    {
        return view('admin.lms.attendance.create-session', compact('course'));
    }

    public function storeSession(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'session_date' => 'required|date',
            'start_time'   => 'nullable|date_format:H:i',
            'end_time'     => 'nullable|date_format:H:i',
            'type'         => 'required|in:online,physical,hybrid',
            'notes'        => 'nullable|string',
        ]);

        $validated['course_id'] = $course->id;

        $session = AttendanceSession::create($validated);

        return redirect()->route('admin.attendance.mark', $session)
            ->with('success', 'Session created successfully.');
    }

    public function markAttendance(AttendanceSession $session)
    {
        $session->load('course');

        $enrollments = Enrollment::where('course_id', $session->course_id)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $records = AttendanceRecord::where('attendance_session_id', $session->id)
            ->get()
            ->keyBy('enrollment_id');

        return view('admin.lms.attendance.mark', compact('session', 'enrollments', 'records'));
    }

    public function saveAttendance(Request $request, AttendanceSession $session)
    {
        $request->validate([
            'records'   => 'required|array',
            'records.*' => 'required|string|in:present,absent,late,excused',
        ]);

        app(AttendanceService::class)->markBulk($session, $request->records);

        return redirect()->back()->with('success', 'Attendance saved successfully.');
    }

    public function report(Course $course)
    {
        $sessions = AttendanceSession::where('course_id', $course->id)->get();

        $enrollments = Enrollment::where('course_id', $course->id)
            ->where('status', 'active')
            ->with('user')
            ->get();

        $attendanceService = app(AttendanceService::class);

        $enrollments = $enrollments->map(function ($enrollment) use ($attendanceService, $course) {
            $enrollment->attendance_percentage = $attendanceService->getStudentPercentage($enrollment, $course);
            return $enrollment;
        });

        $sessionsCount = $sessions->count();

        return view('admin.lms.attendance.report', compact('course', 'enrollments', 'sessionsCount'));
    }

    public function settings(Course $course)
    {
        $settings = AttendanceSettings::firstOrCreate(['course_id' => $course->id]);

        return view('admin.lms.attendance.settings', compact('course', 'settings'));
    }

    public function saveSettings(Request $request, Course $course)
    {
        $validated = $request->validate([
            'minimum_percentage'              => 'required|numeric|min:0|max:100',
            'notify_threshold'                => 'required|numeric|min:0|max:100',
            'notify_student_below_threshold'  => 'boolean',
            'notify_admin_below_threshold'    => 'boolean',
        ]);

        AttendanceSettings::updateOrCreate(
            ['course_id' => $course->id],
            $validated
        );

        return redirect()->back()->with('success', 'Attendance settings saved successfully.');
    }

    public function destroy(AttendanceSession $session)
    {
        $session->delete();

        return redirect()->back()->with('success', 'Attendance session deleted successfully.');
    }
}
