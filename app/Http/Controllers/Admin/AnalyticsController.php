<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Services\ReportingService;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    public function __construct(
        protected ReportingService $reporting
    ) {
    }

    public function dashboard()
    {
        $overview = $this->reporting->platformOverview();
        $trend = $this->reporting->engagementTrend(30);
        $topCourses = $this->reporting->topCourses(8);

        return view('admin.analytics.dashboard', compact('overview', 'trend', 'topCourses'));
    }

    public function course(Course $course)
    {
        $analytics = $this->reporting->courseAnalytics($course);

        return view('admin.analytics.course', compact('course', 'analytics'));
    }

    public function gradeBook(Course $course)
    {
        $data = $this->reporting->gradeBook($course);

        return view('admin.analytics.gradebook', array_merge(['course' => $course], $data));
    }

    public function export(Request $request, string $type)
    {
        $courseId = $request->query('course_id');
        $csv = $this->reporting->exportCsv($type, $courseId);

        $filename = $type . '_report_' . now()->format('Y-m-d') . '.csv';

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
