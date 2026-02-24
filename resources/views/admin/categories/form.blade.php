@extends('layouts.app')

@section('title', ($category ? 'Edit' : 'Create') . ' Category — Green Paw LMS')
@section('page_title', ($category ? 'Edit' : 'Create') . ' Category')

@section('content')
    <div style="max-width: 500px;">
        <div class="card">
            <div class="card-body">
                <form method="POST"
                    action="{{ $category ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
                    @csrf
                    @if($category) @method('PUT') @endif

                    <div class="form-group">
                        <label class="form-label" for="name">Category name</label>
                        <input type="text" name="name" id="name" class="form-input"
                            value="{{ old('name', $category?->name) }}" required placeholder="e.g. Web Development">
                        @error('name') <p class="form-error">{{ $message }}</p> @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="parent_id">Parent category (optional)</label>
                        <select name="parent_id" id="parent_id" class="form-input form-select">
                            <option value="">— None (top-level) —</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}" {{ old('parent_id', $category?->parent_id) == $parent->id ? 'selected' : '' }}>
                                    {{ $parent->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sort_order">Sort order</label>
                        <input type="number" name="sort_order" id="sort_order" class="form-input"
                            value="{{ old('sort_order', $category?->sort_order ?? 0) }}">
                    </div>

                    <div style="display: flex; gap: 12px;">
                        <button type="submit" class="btn btn-primary"
                            style="width: auto;">{{ $category ? 'Update' : 'Create' }} Category</button>
                        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary"
                            style="width: auto;">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection