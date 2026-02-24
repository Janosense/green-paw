@extends('layouts.app')

@section('title', ($announcement ? 'Edit' : 'New') . ' Announcement — Admin')
@section('page_title', ($announcement ? 'Edit' : 'New') . ' Announcement')

@section('content')
    <div class="card" style="max-width: 700px;">
        <div class="card-body">
            <form method="POST"
                action="{{ $announcement ? route('admin.announcements.update', $announcement) : route('admin.announcements.store') }}">
                @csrf
                @if($announcement) @method('PUT') @endif

                <div class="form-group">
                    <label class="form-label" for="title">Title</label>
                    <input type="text" name="title" id="title" class="form-input"
                        value="{{ old('title', $announcement?->title) }}" required>
                    @error('title') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="body">Body</label>
                    <textarea name="body" id="body" class="form-input form-textarea" rows="6"
                        required>{{ old('body', $announcement?->body) }}</textarea>
                    @error('body') <p class="form-error">{{ $message }}</p> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="course_id">Scope</label>
                    <select name="course_id" id="course_id" class="form-input form-select">
                        <option value="">🌐 Global (all users)</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ old('course_id', $announcement?->course_id) == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="display: flex; align-items: center; gap: 8px;">
                    <input type="checkbox" name="publish_now" value="1" id="publish_now" {{ old('publish_now', $announcement?->isPublished() ? '1' : '') ? 'checked' : '' }}>
                    <label for="publish_now" style="cursor: pointer;">Publish immediately</label>
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn btn-primary"
                        style="width: auto;">{{ $announcement ? 'Update' : 'Create' }} Announcement</button>
                    <a href="{{ route('admin.announcements.index') }}" class="btn btn-secondary"
                        style="width: auto;">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection