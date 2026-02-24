<?php

namespace App\Mcp\Tools;

use App\Models\Course;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Tool;

#[Description('Lists courses in the LMS with optional filters by status, level, or search query.')]
class ListCoursesTool extends Tool
{
    public function handle(Request $request): Response
    {
        if ($request->user() && !$request->user()->can('courses.view')) {
            return Response::error('Permission denied.');
        }

        $query = Course::with('instructor', 'categories', 'lessons');

        if ($search = $request->get('search')) {
            $query->search($search);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($level = $request->get('level')) {
            $query->byLevel($level);
        }

        $limit = min((int) ($request->get('limit') ?? 20), 100);
        $courses = $query->latest()->limit($limit)->get();

        $result = $courses->map(fn(Course $c) => [
            'id' => $c->id,
            'title' => $c->title,
            'slug' => $c->slug,
            'status' => $c->status,
            'level' => $c->level,
            'instructor' => $c->instructor->name,
            'lessons_count' => $c->lessons->count(),
            'categories' => $c->categories->pluck('name')->toArray(),
        ])->toArray();

        return Response::text(json_encode($result, JSON_PRETTY_PRINT));
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'search' => $schema->string()->description('Search by title or description.')->optional(),
            'status' => $schema->string()->description('Filter by status: draft, published, archived.')->optional(),
            'level' => $schema->string()->description('Filter by level: beginner, intermediate, advanced.')->optional(),
            'limit' => $schema->integer()->description('Max results (default 20, max 100).')->optional(),
        ];
    }
}
