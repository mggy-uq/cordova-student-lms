<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentCoursesController extends Controller
{
    public function __invoke(Request $request): View
    {
        $student = $request->user();

        $courses = $student->courses()
            ->withCount('assignments')
            ->orderBy('title')
            ->get();

        return view('courses.index', [
            'student' => $student,
            'courses' => $courses,
        ]);
    }
}
