<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;

class CourseController extends Controller
{
    //
    public function coursesList()
    {
        try {
            $courses = Course::select('names', 'thumbnail', 'lessons_count', 'price', 'id')->get();

            return response()->json([
                "status" => true,
                "message" => "success message",
                "data" => $courses,
            ], 200,);
        } catch (\Throwable $th) {
            return response()->json([
                "status" => false,
                "message" => $th->getMessage(),
                "data" => $th->getTrace(),
            ], 500);
        }
    }
}
