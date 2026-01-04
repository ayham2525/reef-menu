{{-- resources/views/admin/positions/edit.blade.php --}}
@extends('adminlte::page')

@section('title', __('Positions') . ' - ' . __('Edit'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-briefcase text-primary mr-2"></i>{{ __('Edit Position') }}
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.positions.index') }}" class="btn btn-secondary mr-2">
                <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.positions.create') }}" class="btn btn-primary custom-btn">
                <i class="fa fa-plus mr-1"></i>{{ __('New Position') }}
            </a>
        </div>
    </div>
@stop

@section('content')

    {{-- Flash success --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa fa-check-circle mr-2"></i>{{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa fa-exclamation-triangle mr-2"></i>
            <strong>{{ __('Please fix the following errors:') }}</strong>
            <ul class="mb-0 mt-2 pl-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <form action="{{ route('admin.positions.update', $position) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="card shadow-sm">
            <div class="card-header">
                <h3 class="card-title mb-0">
                    <i class="fa fa-edit mr-2 text-primary"></i>{{ __('Edit Position') }}
                </h3>
            </div>

            <div class="card-body">
                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">
                                <i class="fa fa-tag mr-1 text-muted"></i>{{ __('Name') }}
                                <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-font"></i></span>
                                </div>
                                <input type="text"
                                       name="name"
                                       id="name"
                                       value="{{ old('name', $position->name) }}"
                                       class="form-control @error('name') is-invalid @enderror"
                                       placeholder="{{ __('e.g., RM , Manager , CEO') }}"
                                       required>
                            </div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Code --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="code">
                                <i class="fa fa-code mr-1 text-muted"></i>{{ __('Code') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-hashtag"></i></span>
                                </div>
                                <input type="text"
                                       name="code"
                                       id="code"
                                       value="{{ old('code', $position->code) }}"
                                       class="form-control @error('code') is-invalid @enderror"
                                       placeholder="{{ __('Short code (optional)') }}">
                            </div>
                            @error('code')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug (locked by default on edit) --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">
                                <i class="fa fa-link mr-1 text-muted"></i>{{ __('Slug') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-link"></i></span>
                                </div>
                                <input type="text"
                                       name="slug"
                                       id="slug"
                                       value="{{ old('slug', $position->slug) }}"
                                       class="form-control @error('slug') is-invalid @enderror"
                                       placeholder="{{ __('auto-generated-from-name') }}"
                                       readonly>
                                <div class="input-group-append">
                                    <button type="button" id="toggle-slug-edit" class="btn btn-outline-secondary"
                                            data-toggle="tooltip" title="{{ __('Edit slug') }}">
                                        <i class="fa fa-lock"></i>
                                    </button>
                                    <button type="button" id="sync-slug" class="btn btn-outline-secondary"
                                            data-toggle="tooltip" title="{{ __('Sync from name') }}">
                                        <i class="fa fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>
                            <small class="form-text text-muted">
                                {{ __('Locked by default to avoid URL changes. Click the lock to edit or use sync to regenerate from name.') }}
                            </small>
                            @error('slug')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Sort Order --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="sort_order">
                                <i class="fa fa-sort-numeric-down mr-1 text-muted"></i>{{ __('Sort Order') }}
                            </label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-sort-amount-down"></i></span>
                                </div>
                                <input type="number"
                                       name="sort_order"
                                       id="sort_order"
                                       value="{{ old('sort_order', $position->sort_order ?? 0) }}"
                                       min="0" step="1"
                                       class="form-control @error('sort_order') is-invalid @enderror">
                            </div>
                            <small class="form-text text-muted">
                                {{ __('Lower numbers appear first.') }}
                            </small>
                            @error('sort_order')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Active --}}
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="d-block">
                                <i class="fa fa-toggle-on mr-1 text-muted"></i>{{ __('Active') }}
                            </label>
                            <div class="custom-control custom-switch mt-2">
                                <input type="checkbox"
                                       class="custom-control-input"
                                       id="is_active"
                                       name="is_active"
                                       value="1"
                                       {{ old('is_active', $position->is_active) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">
                                    {{ __('Enable this position') }}
                                </label>
                            </div>
                            @error('is_active')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <label for="description">
                                <i class="fa fa-align-left mr-1 text-muted"></i>{{ __('Description') }}
                            </label>
                            <textarea
                                name="description"
                                id="description"
                                rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                                placeholder="{{ __('Optional notes or details') }}">{{ old('description', $position->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

            <div class="card-footer d-flex justify-content-between">
                <a href="{{ route('admin.positions.index') }}" class="btn btn-outline-secondary">
                    <i class="fa fa-times mr-1"></i>{{ __('Cancel') }}
                </a>
                <button type="submit" class="btn btn-primary custom-btn">
                    <i class="fa fa-save mr-1"></i>{{ __('Save Changes') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('js')
<script>
    function slugify(str) {
        return str
            .toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .replace(/--+/g, '-');
    }

    (function () {
        const nameInput = document.getElementById('name');
        const slugInput = document.getElementById('slug');
        const toggleBtn = document.getElementById('toggle-slug-edit');
        const syncBtn   = document.getElementById('sync-slug');

        // On EDIT, keep slug locked and NOT auto-updating
        let manualSlug = true;         // prevents auto updates from name
        slugInput.readOnly = true;

        // Toggle lock/unlock for manual editing
        toggleBtn.addEventListener('click', function () {
            const locked = slugInput.readOnly;
            slugInput.readOnly = !locked;
            this.innerHTML = locked ? '<i class="fa fa-unlock"></i>' : '<i class="fa fa-lock"></i>';
            this.title = locked ? '{{ __('Lock slug') }}' : '{{ __('Edit slug') }}';
            if (!locked) slugInput.blur(); else slugInput.focus();
        });

        // Explicitly sync slug from name when user clicks sync
        syncBtn.addEventListener('click', function () {
            slugInput.value = slugify(nameInput.value || '');
            // keep it locked unless user unlocked
        });

        // If user starts typing in slug, we respect manual edits
        slugInput.addEventListener('input', function () {
            manualSlug = true;
        });

        // (Optional) If you want to auto-update only when slug is empty:
        nameInput.addEventListener('input', function () {
            if (!slugInput.value && !manualSlug) {
                slugInput.value = slugify(this.value);
            }
        });
    })();
</script>
@stop
