<?php

namespace App\Mcp\Tools;

use App\Models\Course;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists lessons for a specific course.')]
class ListLessonsTool extends Tool
{
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('lessons.view')) {
            return Response::error('Permission denied.');
        }

        $course = Course::with('lessons')->find($request->get('course_id'));
        if (!$course) {
            return Response::error('Course not found.');
        }

        $result = $course->lessons->map(fn($l) => [
            'id' => $l->id,
            'title' => $l->title,
            'content_type' => $l->content_type,
            'duration_minutes' => $l->duration_minutes,
            'sort_order' => $l->sort_order,
            'is_published' => $l->is_published,
            'is_free_preview' => $l->is_free_preview,
        ])->toArray();

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'course_id' => $schema->integer()->description('ID of the course.')->required(),
        ];
    }
}
