<?php

namespace App\Mcp\Tools;

use App\Models\Course;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Creates a new course in the LMS.')]
class CreateCourseTool extends Tool
{
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('courses.create')) {
            return Response::error('Permission denied.');
        }

        $course = Course::create([
            'instructor_id' => $request->user()?->id ?? 1,
            'title' => $request->get('title'),
            'short_description' => $request->get('short_description'),
            'description' => $request->get('description'),
            'level' => $request->get('level') ?? 'beginner',
        ]);

        return Response::text(json_encode([
            'success' => true,
            'course' => [
                'id' => $course->id,
                'title' => $course->title,
                'slug' => $course->slug,
                'status' => $course->status,
            ],
        ], JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'title' => $schema->string()->description('Course title.')->required(),
            'short_description' => $schema->string()->description('Brief catalog summary.')->optional(),
            'description' => $schema->string()->description('Full course description.')->optional(),
            'level' => $schema->string()->description('beginner, intermediate, or advanced.')->optional(),
        ];
    }
}
