<?php

namespace App\Http\Middleware;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCourseEnrollmentAccess
{
    public function handle(
        Request $request,
        Closure $next
    ): Response|RedirectResponse {
        $user = $request->user();

        $course = $request->route('course');

        if (
            ! $user
            || ! $course instanceof Course
        ) {
            return $next($request);
        }

        $enrollment = CourseEnrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        /*
         * Visitors who have not enrolled may still view the
         * course page and enrol normally. Existing access
         * restrictions apply only to an enrolment record.
         */
        if (! $enrollment) {
            return $next($request);
        }

        if ($enrollment->blocksLearningAccess()) {
            return redirect()->route(
                'learn.courses.access-restricted',
                $course
            );
        }

        return $next($request);
    }
}