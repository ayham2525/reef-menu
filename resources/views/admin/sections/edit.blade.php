@extends('adminlte::page')

@section('title', __('Edit Section'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-sitemap text-primary"></i> {{ __('Edit Section') }} — {{ $section->name }}
        </h1>
        <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> {{ __('Back to Sections') }}
        </a>
    </div>
@stop

@section('content')
    {{-- Error summary (closable) --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Please fix the errors below:') }}</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- UPDATE form --}}
    <form id="form-update" action="{{ route('admin.sections.update', $section) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('Section Details') }}</h3>
            </div>

            <div class="card-body">
                {{-- Name --}}
                <div class="form-group">
                    <label for="name"><i class="fa fa-tag mr-1 text-muted"></i> {{ __('Name') }}</label>
                    <input type="text" id="name" name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $section->name) }}" required autofocus>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Code & Slug --}}
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="code"><i class="fa fa-code mr-1 text-muted"></i> {{ __('Code') }}</label>
                        <input type="text" id="code" name="code"
                               class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $section->code) }}">
                        @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="slug"><i class="fa fa-link mr-1 text-muted"></i> {{ __('Slug') }}</label>
                        <input type="text" id="slug" name="slug"
                               class="form-control @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $section->slug) }}">
                        @error('slug') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <small class="form-text text-muted">{{ __('Leave empty to auto-generate from Name.') }}</small>
                    </div>
                </div>

                {{-- Parent --}}
                <div class="form-group">
                    <label for="parent_id"><i class="fa fa-sitemap mr-1 text-muted"></i> {{ __('Parent Section') }}</label>
                    <select id="parent_id" name="parent_id"
                            class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">{{ __('-- None --') }}</option>
                        @foreach ($parents as $p)
                            <option value="{{ $p->id }}" @selected(old('parent_id', $section->parent_id) == $p->id)>
                                {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('parent_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Description --}}
                <div class="form-group">
                    <label for="description"><i class="fa fa-align-left mr-1 text-muted"></i> {{ __('Description') }}</label>
                    <textarea id="description" name="description" rows="3"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $section->description) }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Sort & Status --}}
                <div class="form-row">
                    <div class="form-group col-sm-6">
                        <label for="sort_order"><i class="fa fa-sort-numeric-down mr-1 text-muted"></i> {{ __('Sort Order') }}</label>
                        <input type="number" min="0" id="sort_order" name="sort_order"
                               class="form-control @error('sort_order') is-invalid @enderror"
                               value="{{ old('sort_order', $section->sort_order) }}">
                        @error('sort_order') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="form-group col-sm-6">
                        <label for="is_active"><i class="fa fa-toggle-on mr-1 text-muted"></i> {{ __('Status') }}</label>
                        <select id="is_active" name="is_active"
                                class="form-control @error('is_active') is-invalid @enderror">
                            <option value="1" @selected(old('is_active', (int)$section->is_active) == 1)>{{ __('Active') }}</option>
                            <option value="0" @selected(old('is_active', (int)$section->is_active) == 0)>{{ __('Inactive') }}</option>
                        </select>
                        @error('is_active') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                </div>
            </div>

            <div class="card-footer d-flex justify-content-between align-items-center">
                <a href="{{ route('admin.sections.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-arrow-left"></i> {{ __('Back') }}
                </a>

                <div class="d-flex align-items-center">
                    {{-- Delete triggers the separate form below --}}
                    <button type="button" class="btn btn-outline-danger mr-2"
                            onclick="if(confirm('{{ __('Delete this section?') }}')) document.getElementById('form-delete').submit();">
                        <i class="fa fa-trash"></i> {{ __('Delete') }}
                    </button>

                    {{-- Update submits THIS form --}}
                    <button type="submit" class="btn btn-primary custom-btn">
                        <i class="fa fa-save mr-1"></i> {{ __('Update') }}
                    </button>
                </div>
            </div>
        </div>
    </form>

    {{-- Standalone DELETE form --}}
    <form id="form-delete" action="{{ route('admin.sections.destroy', $section) }}" method="POST" class="d-none">
        @csrf
        @method('DELETE')
    </form>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const name = document.getElementById('name');
    const slug = document.getElementById('slug');

    function slugify(str) {
        return str
            .toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .trim()
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .substring(0, 160);
    }

    // Auto-generate slug if empty
    name.addEventListener('input', () => {
        if (!slug.value) slug.value = slugify(name.value);
    });
});
</script>
@stop
