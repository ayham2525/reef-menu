{{-- resources/views/admin/menu_categories/create.blade.php --}}
@extends('adminlte::page')

@section('title', __('Menu Categories') . ' - ' . __('Create'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-plus-circle text-primary mr-2"></i>{{ __('Create Category') }}
    </h1>
    <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-secondary">
        <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
    </a>
</div>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><strong>{{ __('Please fix the errors below') }}</strong></div>
@endif

<form action="{{ route('admin.menu-categories.store') }}" method="POST" autocomplete="off">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header"><i class="fa fa-layer-group mr-1"></i>{{ __('Category Details') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label>{{ __('Code') }}</label>
                    <input type="text" name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror">
                    @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label>{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" step="1" class="form-control @error('sort_order') is-invalid @enderror">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Slug') }}</label>
                    <div class="input-group">
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="{{ __('auto-generated-from-name') }}" readonly>
                        <div class="input-group-append">
                            <button type="button" id="toggle-slug-edit" class="btn btn-outline-secondary" data-toggle="tooltip" title="{{ __('Edit slug') }}"><i class="fa fa-lock"></i></button>
                            <button type="button" id="sync-slug" class="btn btn-outline-secondary" data-toggle="tooltip" title="{{ __('Sync from name') }}"><i class="fa fa-sync-alt"></i></button>
                        </div>
                    </div>
                    <small class="form-text text-muted">{{ __('Locked by default. Click the lock to edit or use sync to regenerate from name.') }}</small>
                    @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Parent Category') }}</label>
                    <select name="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">{{ __('None') }}</option>
                        @foreach($parents as $p)
                            <option value="{{ $p->id }}" {{ old('parent_id')==$p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label>{{ __('Description') }}</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label class="d-block">{{ __('Active') }}</label>
                    <input type="hidden" name="is_active" value="0">
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="custom-control-label" for="is_active">{{ __('Enable this category') }}</label>
                    </div>
                    @error('is_active')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary custom-btn"><i class="fa fa-save mr-1"></i>{{ __('Save') }}</button>
        </div>
    </div>
</form>
@stop

@section('js')
<script>
(function(){
    function slugify(str){
        return (str||'').toString()
            .normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .toLowerCase().replace(/[^a-z0-9]+/g,'-')
            .replace(/^-+|-+$/g,'').replace(/--+/g,'-');
    }
    const nameInput = document.querySelector('input[name="name"]');
    const slugInput = document.getElementById('slug');
    const toggleBtn = document.getElementById('toggle-slug-edit');
    const syncBtn   = document.getElementById('sync-slug');

    let manualSlug = true;
    slugInput.readOnly = true;

    toggleBtn?.addEventListener('click', function(){
        const locked = slugInput.readOnly;
        slugInput.readOnly = !locked;
        this.innerHTML = locked ? '<i class="fa fa-unlock"></i>' : '<i class="fa fa-lock"></i>';
        this.title = locked ? '{{ __('Lock slug') }}' : '{{ __('Edit slug') }}';
        if (!locked) slugInput.focus(); else slugInput.blur();
    });

    syncBtn?.addEventListener('click', function(){
        slugInput.value = slugify(nameInput.value || '');
    });

    slugInput?.addEventListener('input', ()=> manualSlug = true);
})();
</script>
@stop
