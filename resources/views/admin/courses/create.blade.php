@extends('layouts.app')

@section('title', 'Create Course — Green Paw LMS')
@section('page_title', 'Create Course')

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="title">Course title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. Introduction to PHP">
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="short_description">Short description</label>
                        <textarea name="short_description" id="short_description" class="form-input form-textarea" rows="2" placeholder="Brief summary for the catalog..." style="min-height: 60px;">{{ old('short_description') }}</textarea>
                        @error('short_description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Full description</label>
                        <textarea name="description" id="description" class="form-input form-textarea" rows="6" placeholder="Detailed course description...">{{ old('description') }}</textarea>
                        @error('description') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="level">Level</label>
                            <select name="level" id="level" class="form-input form-select" required>
                                <option value="beginner" {{ old('level') === 'beginner' ? 'selected' : '' }}>Beginner</option>
                                <option value="intermediate" {{ old('level') === 'intermediate' ? 'selected' : '' }}>Intermediate</option>
                                <option value="advanced" {{ old('level') === 'advanced' ? 'selected' : '' }}>Advanced</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="duration_minutes">Duration (minutes)</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-input" value="{{ old('duration_minutes') }}" placeholder="Estimated total">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="thumbnail">Thumbnail</label>
                        <input type="file" name="thumbnail" id="thumbnail" class="form-input" accept="image/*" style="padding: 8px;">
                        @error('thumbnail') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Price (optional)</label>
                        <input type="number" name="price" id="price" class="form-input" value="{{ old('price') }}" step="0.01" min="0" placeholder="0.00 = free">
                    </div>

                    @if($categories->count())
                    <div class="form-group">
                        <label class="form-label">Categories</label>
                        <div class="checkbox-grid">
                            @foreach($categories as $cat)
                                <div class="checkbox-item">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}"
                                        {{ in_array($cat->id, old('categories', [])) ? 'checked' : '' }}>
                                    <label for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                                </div>
                                @foreach($cat->children as $child)
                                    <div class="checkbox-item" style="padding-left: 28px;">
                                        <input type="checkbox" name="categories[]" value="{{ $child->id }}" id="cat_{{ $child->id }}"
                                            {{ in_array($child->id, old('categories', [])) ? 'checked' : '' }}>
                                        <label for="cat_{{ $child->id }}">{{ $child->name }}</label>
                                    </div>
                                @endforeach
                            @endforeach
                        </div>
                    </div>
                    @endif

                    @if($allCourses->count())
                    <div class="form-group">
                        <label class="form-label">Prerequisites</label>
                        <select name="prerequisites[]" multiple class="form-input form-select" style="min-height: 100px;">
                            @foreach($allCourses as $prereq)
                                <option value="{{ $prereq->id }}" {{ in_array($prereq->id, old('prerequisites', [])) ? 'selected' : '' }}>
                                    {{ $prereq->title }}
                                </option>
                            @endforeach
                        </select>
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Hold Cmd/Ctrl to select multiple</p>
                    </div>
                    @endif

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Create Course</button>
                        <a href="{{ route('admin.courses.index') }}" class="btn btn-secondary" style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
