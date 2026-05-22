<?php
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AttendanceRecord;
use App\Models\AttendanceSession;
use App\Models\Course;
use App\Models\Enrollment;
use App\Services\LMS\AttendanceService;

class AttendanceController extends Controller
{
    public function index()
    {
        $enrollments = Enrollment::where('user_id', auth()->id())
            ->where('status', 'active')
            ->with('course')
            ->get();

        $service = new AttendanceService();
        $enrollments->each(function ($enrollment) use ($service) {
            $enrollment->attendance_percentage = $service->getStudentPercentage(
                auth()->id(),
                $enrollment->course_id
            );
        });

        return view('student.attendance.index', compact('enrollments'));
    }

    public function show(Course $course)
    {
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('course_id', $course->id)
            ->firstOrFail();

        $sessions = AttendanceSession::where('course_id', $course->id)
            ->orderByDesc('session_date')
            ->get();

        $records = AttendanceRecord::where('user_id', auth()->id())
            ->whereIn('session_id', $sessions->pluck('id'))
            ->get()
            ->keyBy('session_id');

        $percentage = (new AttendanceService())->getStudentPercentage(auth()->id(), $course->id);

        return view('student.attendance.show', compact('course', 'sessions', 'records', 'percentage'));
    }
}
