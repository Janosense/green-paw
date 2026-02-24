@extends('layouts.app')

@section('title', 'Edit Lesson — Green Paw LMS')
@section('page_title', 'Edit Lesson')

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.lessons.update', [$course, $lesson]) }}"
                    enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="title">Lesson title</label>
                        <input type="text" name="title" id="title" class="form-input"
                            value="{{ old('title', $lesson->title) }}" required>
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea name="description" id="description" class="form-input form-textarea" rows="2"
                            style="min-height: 60px;">{{ old('description', $lesson->description) }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="content_type">Content type</label>
                            <select name="content_type" id="content_type" class="form-input form-select" required
                                onchange="toggleContent(this.value)">
                                @foreach(['text' => '📝 Text', 'video' => '🎬 Video', 'audio' => '🎧 Audio', 'pdf' => '📄 PDF', 'html' => '🌐 HTML'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('content_type', $lesson->content_type) === $val ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="duration_minutes">Duration (min)</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-input"
                                value="{{ old('duration_minutes', $lesson->duration_minutes) }}" min="1">
                        </div>
                    </div>

                    <div class="form-group" id="content-field">
                        <label class="form-label" for="content">Content</label>
                        <textarea name="content" id="content" class="form-input form-textarea"
                            rows="10">{{ old('content', $lesson->content) }}</textarea>
                    </div>

                    <div class="form-group" id="media-url-field" style="display: none;">
                        <label class="form-label" for="media_url">Media URL</label>
                        <input type="url" name="media_url" id="media_url" class="form-input"
                            value="{{ old('media_url', $lesson->media_url) }}">
                        @if($lesson->media_url)
                            <p style="font-size: 12px; color: var(--text-secondary); margin-top: 4px;">Current: <code
                                    style="color: var(--accent);">{{ $lesson->media_url }}</code></p>
                        @endif
                    </div>

                    <div class="form-group" id="media-file-field" style="display: none;">
                        <label class="form-label" for="media_file">Upload new file</label>
                        <input type="file" name="media_file" id="media_file" class="form-input" style="padding: 8px;">
                    </div>

                    <div class="grid-2" style="margin-bottom: 20px;">
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_free_preview" value="1" id="is_free_preview" {{ old('is_free_preview', $lesson->is_free_preview) ? 'checked' : '' }}>
                            <label for="is_free_preview">Free preview</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', $lesson->is_published) ? 'checked' : '' }}>
                            <label for="is_published">Published</label>
                        </div>
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Update Lesson</button>
                        <a href="{{ route('admin.courses.edit', $course) }}" class="btn btn-secondary"
                            style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function toggleContent(type) {
            const contentField = document.getElementById('content-field');
            const mediaUrlField = document.getElementById('media-url-field');
            const mediaFileField = document.getElementById('media-file-field');
            const mediaTypes = ['video', 'audio', 'pdf'];
            const textTypes = ['text', 'html'];
            contentField.style.display = textTypes.includes(type) ? 'block' : 'none';
            mediaUrlField.style.display = mediaTypes.includes(type) ? 'block' : 'none';
            mediaFileField.style.display = mediaTypes.includes(type) ? 'block' : 'none';
        }
        toggleContent(document.getElementById('content_type').value);
    </script>
@endpush