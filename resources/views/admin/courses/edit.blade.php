@extends('layouts.app')

@section('title', 'Edit: {{ $course->title }} — Green Paw LMS')
@section('page_title', 'Edit Course')

@section('topbar_actions')
    @if($course->status === 'draft')
        <form method="POST" action="{{ route('admin.courses.publish', $course) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-primary btn-sm">Publish</button>
        </form>
    @else
        <form method="POST" action="{{ route('admin.courses.unpublish', $course) }}" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-outline-accent btn-sm">Unpublish</button>
        </form>
    @endif
    <form method="POST" action="{{ route('admin.courses.new-version', $course) }}" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm">New Version</button>
    </form>
@endsection

@section('content')
    <div class="grid-2" style="grid-template-columns: 1fr 380px; align-items: start;">
        <!-- Left: Course details form -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Course Details</h3>
                <div style="display: flex; gap: 6px;">
                    @php $statusColors = ['draft' => 'badge-warning', 'published' => 'badge-accent', 'archived' => 'badge-muted']; @endphp
                    <span class="badge {{ $statusColors[$course->status] }}">{{ ucfirst($course->status) }}</span>
                    <span class="badge badge-muted">v{{ $course->version }}</span>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.courses.update', $course) }}" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="title">Course title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $course->title) }}" required>
                        @error('title') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="short_description">Short description</label>
                        <textarea name="short_description" id="short_description" class="form-input form-textarea" rows="2" style="min-height: 60px;">{{ old('short_description', $course->short_description) }}</textarea>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="description">Full description</label>
                        <textarea name="description" id="description" class="form-input form-textarea" rows="5">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div class="grid-2">
                        <div class="form-group">
                            <label class="form-label" for="level">Level</label>
                            <select name="level" id="level" class="form-input form-select">
                                @foreach(['beginner', 'intermediate', 'advanced'] as $lvl)
                                    <option value="{{ $lvl }}" {{ old('level', $course->level) === $lvl ? 'selected' : '' }}>{{ ucfirst($lvl) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="duration_minutes">Duration (min)</label>
                            <input type="number" name="duration_minutes" id="duration_minutes" class="form-input" value="{{ old('duration_minutes', $course->duration_minutes) }}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Thumbnail</label>
                        <div style="display: flex; align-items: center; gap: 16px;">
                            @if($course->thumbnail)
                                <img src="{{ Storage::url($course->thumbnail) }}" alt="" style="width: 80px; height: 50px; object-fit: cover; border-radius: var(--radius-sm); border: 1px solid var(--border);">
                            @endif
                            <input type="file" name="thumbnail" class="form-input" accept="image/*" style="padding: 8px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Price</label>
                        <input type="number" name="price" class="form-input" value="{{ old('price', $course->price) }}" step="0.01" min="0">
                    </div>

                    @if($categories->count())
                    <div class="form-group">
                        <label class="form-label">Categories</label>
                        <div class="checkbox-grid">
                            @php $selectedCats = old('categories', $course->categories->pluck('id')->toArray()); @endphp
                            @foreach($categories as $cat)
                                <div class="checkbox-item">
                                    <input type="checkbox" name="categories[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}"
                                        {{ in_array($cat->id, $selectedCats) ? 'checked' : '' }}>
                                    <label for="cat_{{ $cat->id }}">{{ $cat->name }}</label>
                                </div>
                                @foreach($cat->children as $child)
                                    <div class="checkbox-item" style="padding-left: 28px;">
                                        <input type="checkbox" name="categories[]" value="{{ $child->id }}" id="cat_{{ $child->id }}"
                                            {{ in_array($child->id, $selectedCats) ? 'checked' : '' }}>
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
                        @php $selectedPrereqs = old('prerequisites', $course->prerequisites->pluck('id')->toArray()); @endphp
                        <select name="prerequisites[]" multiple class="form-input form-select" style="min-height: 80px;">
                            @foreach($allCourses as $prereq)
                                <option value="{{ $prereq->id }}" {{ in_array($prereq->id, $selectedPrereqs) ? 'selected' : '' }}>
                                    {{ $prereq->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <button type="submit" class="btn btn-primary" style="width: auto;">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Right: Lessons panel -->
        <div>
            <div class="card" style="margin-bottom: 16px;">
                <div class="card-header">
                    <h3 class="card-title">Lessons ({{ $course->lessons->count() }})</h3>
                    <a href="{{ route('admin.courses.lessons.create', $course) }}" class="btn btn-primary btn-sm">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add
                    </a>
                </div>
                <div id="lessons-list">
                    @forelse($course->lessons as $lesson)
                    <div class="lesson-item" data-id="{{ $lesson->id }}" style="display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-bottom: 1px solid var(--border); cursor: grab;">
                        <span style="color: var(--text-muted); font-size: 16px; cursor: grab;">⠿</span>
                        <span style="font-size: 16px;">{{ $lesson->contentTypeIcon() }}</span>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $lesson->title }}</div>
                            <div style="font-size: 12px; color: var(--text-muted);">
                                {{ ucfirst($lesson->content_type) }}
                                @if($lesson->duration_minutes) · {{ $lesson->duration_minutes }}min @endif
                                @if($lesson->is_free_preview) · <span style="color: var(--accent);">Preview</span> @endif
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px;">
                            <a href="{{ route('admin.courses.lessons.edit', [$course, $lesson]) }}" class="btn btn-secondary btn-sm" style="padding: 4px 10px;">Edit</a>
                            <form method="POST" action="{{ route('admin.courses.lessons.destroy', [$course, $lesson]) }}" onsubmit="return confirm('Delete this lesson?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" style="padding: 4px 10px;">×</button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div style="padding: 32px 16px; text-align: center; color: var(--text-muted); font-size: 13px;">
                        No lessons yet. Click "Add" to get started.
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Danger zone -->
            <div class="card" style="border-color: rgba(239, 68, 68, 0.2);">
                <div class="card-header">
                    <h3 class="card-title" style="color: var(--danger);">Danger Zone</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.courses.destroy', $course) }}" onsubmit="return confirm('Are you sure? This will delete the course and all its lessons.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">Delete Course</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
// Simple drag-to-reorder
const list = document.getElementById('lessons-list');
let dragged = null;

list?.addEventListener('dragstart', (e) => {
    dragged = e.target.closest('.lesson-item');
    if (dragged) dragged.style.opacity = '0.5';
});

list?.addEventListener('dragend', () => {
    if (dragged) dragged.style.opacity = '1';
    dragged = null;
});

list?.addEventListener('dragover', (e) => {
    e.preventDefault();
    const target = e.target.closest('.lesson-item');
    if (target && target !== dragged) {
        const rect = target.getBoundingClientRect();
        const after = e.clientY > rect.top + rect.height / 2;
        if (after) {
            target.after(dragged);
        } else {
            target.before(dragged);
        }
    }
});

list?.addEventListener('drop', () => {
    const items = list.querySelectorAll('.lesson-item');
    const order = [...items].map(item => parseInt(item.dataset.id));

    fetch('{{ route("admin.courses.lessons.reorder", $course) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ order }),
    });
});

document.querySelectorAll('.lesson-item').forEach(item => {
    item.setAttribute('draggable', 'true');
});
</script>
@endpush
