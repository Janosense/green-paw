@extends('layouts.app')

@section('title', 'Add Lesson — Green Paw LMS')
@section('page_title', 'Add Lesson to: ' . $course->title)

@section('content')
    <div style="max-width: 700px;">
        <div class="card">
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.lessons.store', $course) }}"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label" for="title">Lesson title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" required
                            placeholder="e.g. Getting Started with Variables">
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Description (optional)</label>
                        <textarea name="description" id="description" class="form-input form-textarea" rows="2"
                            style="min-height: 60px;">{{ old('description') }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="content_type">Content type</label>
                            <select name="content_type" id="content_type" class="form-input form-select" required
                                onchange="toggleContent(this.value)">
                                <option value="text" {{ old('content_type') === 'text' ? 'selected' : '' }}>📝 Text</option>
                                <option value="video" {{ old('content_type') === 'video' ? 'selected' : '' }}>🎬 Video
                                </option>
                                <option value="audio" {{ old('content_type') === 'audio' ? 'selected' : '' }}>🎧 Audio
                                </option>
                                <option value="pdf" {{ old('content_type') === 'pdf' ? 'selected' : '' }}>📄 PDF</option>
                                <option value="html" {{ old('content_type') === 'html' ? 'selected' : '' }}>🌐 HTML</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="duration_minutes">Duration (min)</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-input"
                                value="{{ old('duration_minutes') }}" min="1">
                        </div>
                    </div>

                    <div class="form-group" id="content-field">
                        <label class="form-label" for="content">Content</label>
                        <textarea name="content" id="content" class="form-input form-textarea" rows="10"
                            placeholder="Lesson content (text, HTML, or embed code)...">{{ old('content') }}</textarea>
                        @error('content') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group" id="media-url-field" style="display: none;">
                        <label class="form-label" for="media_url">Media URL</label>
                        <input type="url" name="media_url" id="media_url" class="form-input" value="{{ old('media_url') }}"
                            placeholder="https://...">
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">External URL to video, audio,
                            or file</p>
                    </div>

                    <div class="form-group" id="media-file-field" style="display: none;">
                        <label class="form-label" for="media_file">Upload file</label>
                        <input type="file" name="media_file" id="media_file" class="form-input" style="padding: 8px;">
                        <p style="font-size: 12px; color: var(--text-muted); margin-top: 4px;">Max 100MB</p>
                    </div>

                    <div class="grid-2">
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_free_preview" value="1" id="is_free_preview" {{ old('is_free_preview') ? 'checked' : '' }}>
                            <label for="is_free_preview">Free preview</label>
                        </div>
                        <div class="checkbox-item">
                            <input type="checkbox" name="is_published" value="1" id="is_published" {{ old('is_published', true) ? 'checked' : '' }}>
                            <label for="is_published">Published</label>
                        </div>
                    </div>

                    <input type="hidden" name="sort_order" value="{{ $nextOrder }}">

                    <div style="display: flex; gap: 12px; margin-top: 20px;">
                        <button type="submit" class="btn btn-primary" style="width: auto;">Add Lesson</button>
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