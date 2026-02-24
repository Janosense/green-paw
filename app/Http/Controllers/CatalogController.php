<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Course;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Display the public course catalog.
     */
    public function index(Request $request)
    {
        $query = Course::published()->with('instructor', 'categories', 'lessons');

        if ($search = $request->input('search')) {
            $query->search($search);
        }

        if ($category = $request->input('category')) {
            $query->whereHas('categories', function ($q) use ($category) {
                $q->where('categories.slug', $category);
            });
        }

        if ($level = $request->input('level')) {
            $query->byLevel($level);
        }

        $sort = $request->input('sort', 'newest');
        $query = match ($sort) {
            'oldest' => $query->oldest(),
            'title' => $query->orderBy('title'),
            'price_low' => $query->orderBy('price'),
            'price_high' => $query->orderByDesc('price'),
            default => $query->latest('published_at'),
        };

        $courses = $query->paginate(12);
        $categories = Category::roots()->withCount('courses')->get();

        return view('catalog.index', compact('courses', 'categories'));
    }

    /**
     * Display a course detail page.
     */
    public function show(Course $course)
    {
        if (!$course->isPublished()) {
            abort(404);
        }

        $course->load([
            'instructor',
            'categories',
            'lessons' => fn($q) => $q->ordered(),
            'prerequisites' => fn($q) => $q->published(),
        ]);

        return view('catalog.show', compact('course'));
    }
}
