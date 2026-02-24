@extends('layouts.app')

@section('title', ($path ? 'Edit' : 'Create') . ' Learning Path — Green Paw LMS')
@section('page_title', ($path ? 'Edit' : 'Create') . ' Learning Path')

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ $path ? route('admin.learning-paths.update', $path) : route('admin.learning-paths.store') }}">
                    @csrf
                    @if($path) @method('PUT') @endif

                    <div class="form-group">
                        <label class="form-label" for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $path?->title) }}" required placeholder="e.g. Full-Stack Web Developer">
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-input form-textarea" rows="3">{{ old('description', $path?->description) }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="level">Level</label>
                            <select name="level" id="level" class="form-input form-select" required>
                                @foreach(['beginner', 'intermediate', 'advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $path?->level) === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="is_published" style="margin-bottom: 12px;">&nbsp;</label>
                            <div class="checkbox-item">
                                <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $path?->is_published) ? 'checked' : '' }}>
                                <label for="is_published">Published</label>
                            </div>
                        </div>
                    </div>

                    @if($courses->count())
                    <div class="form-group">
                        <label class="form-label">Courses (order matters)</label>
                        @php $selectedCourses = old('courses', $path?->courses->pluck('id')->toArray() ?? []); @endphp
                        <select name="courses[]" multiple class="form-input form-select" style="min-height: 140px;">
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ in_array($course->id, $selectedCourses) ? 'selected' : '' }}>
                                    {{ $course->title }} ({{ ucfirst($course->level) }})
                                </option>
                            @endforeach
                        </select>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Hold Cmd/Ctrl to select multiple. Order of selection is preserved.</p>
                    </div>
                    @endif

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">{{ $path ? 'Update' : 'Create' }} Path</button>
                        <a href="{{ route('admin.learning-paths.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
