@extends('adminlte::page')

@section('title', __('Menu Items') . ' - ' . __('Create'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-plus-circle text-primary mr-2"></i>{{ __('Create Menu Item') }}
    </h1>
    <a href="{{ route('admin.menu-items.index') }}" class="btn btn-secondary">
        <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
    </a>
</div>
@stop

@section('content')
@if ($errors->any())
    <div class="alert alert-danger"><strong>{{ __('Please fix the errors below') }}</strong></div>
@endif

{{-- IMPORTANT: enctype for file uploads --}}
<form action="{{ route('admin.menu-items.store') }}" method="POST" enctype="multipart/form-data" autocomplete="off">
    @csrf
    <div class="card shadow-sm">
        <div class="card-header"><i class="fa fa-info-circle mr-1"></i>{{ __('Item Details') }}</div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>{{ __('Name') }} <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label>{{ __('Category') }}</label>
                    <select name="menu_category_id" class="form-control @error('menu_category_id') is-invalid @enderror">
                        <option value="">{{ __('None') }}</option>
                        @foreach($categories as $c)
                            <option value="{{ $c->id }}" {{ old('menu_category_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                    @error('menu_category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3">
                    <label>{{ __('SKU') }}</label>
                    <input type="text" name="sku" value="{{ old('sku') }}" class="form-control @error('sku') is-invalid @enderror">
                    @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Slug') }}</label>
                    <div class="input-group">
                        <input type="text" name="slug" id="slug" value="{{ old('slug') }}" class="form-control @error('slug') is-invalid @enderror" placeholder="{{ __('auto-generated-from-name') }}" readonly>
                        <div class="input-group-append">
                            <button type="button" id="toggle-slug-edit" class="btn btn-outline-secondary" title="{{ __('Edit slug') }}"><i class="fa fa-lock"></i></button>
                            <button type="button" id="sync-slug" class="btn btn-outline-secondary" title="{{ __('Sync from name') }}"><i class="fa fa-sync-alt"></i></button>
                        </div>
                    </div>
                    <small class="form-text text-muted">{{ __('Locked by default. Click the lock to edit or sync from name.') }}</small>
                    @error('slug')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 mt-3">
                    <label>{{ __('Price') }}</label>
                    <input type="number" name="price" step="0.01" value="{{ old('price', 0) }}" class="form-control @error('price') is-invalid @enderror">
                    @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-3 mt-3">
                    <label>{{ __('Currency') }}</label>
                    <input type="text" name="currency" value="{{ old('currency', 'AED') }}" class="form-control @error('currency') is-invalid @enderror">
                    @error('currency')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mt-3">
                    <label>{{ __('Prep Time (minutes)') }}</label>
                    <input type="number" name="prep_time_minutes" value="{{ old('prep_time_minutes') }}" class="form-control @error('prep_time_minutes') is-invalid @enderror">
                    @error('prep_time_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mt-3">
                    <label>{{ __('Calories') }}</label>
                    <input type="number" name="calories" value="{{ old('calories') }}" class="form-control @error('calories') is-invalid @enderror">
                    @error('calories')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mt-3">
                    <label>{{ __('Sort Order') }}</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="form-control @error('sort_order') is-invalid @enderror">
                    @error('sort_order')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4 mt-3">
                <label>{{ __('Active') }}</label><br>
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1"
                    {{ old('is_active', 1) ? 'checked' : '' }}> {{ __('Enable this item') }}
            </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Tags (comma-separated)') }}</label>
                    <input type="text" name="tags" value="{{ old('tags') }}" class="form-control @error('tags') is-invalid @enderror">
                    @error('tags')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label>{{ __('Allergens (comma-separated)') }}</label>
                    <input type="text" name="allergens" value="{{ old('allergens') }}" class="form-control @error('allergens') is-invalid @enderror">
                    @error('allergens')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label>{{ __('Description') }}</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                {{-- IMAGE UPLOAD --}}
                <div class="col-md-12 mt-3">
                    <label>{{ __('Image') }}</label>
                    <input type="file" name="image" accept="image/*"
                           class="form-control-file @error('image') is-invalid @enderror">
                    @error('image')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    <small class="text-muted d-block mt-1">
                        {{ __('Accepted: JPG, JPEG, PNG, WEBP up to 5MB') }}
                    </small>
                    <img id="image-preview" src="#" alt="" style="display:none;max-height:160px" class="mt-2 img-thumbnail">
                </div>

                {{-- OPTIONS (repeatable via Alpine.js) --}}
                <div class="col-md-12 mt-4" x-data="menuOptions()">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="mb-0"><i class="fa fa-sliders-h mr-1"></i>{{ __('Item Options') }}</h5>
                        <button type="button" class="btn btn-sm btn-outline-primary" @click="addOption()">
                            <i class="fa fa-plus mr-1"></i>{{ __('Add Option') }}
                        </button>
                    </div>

                    <template x-for="(opt, oi) in options" :key="opt._key">
                        <div class="border rounded p-3 mb-3">
                            <div class="form-row">
                                <div class="col-md-4">
                                    <label>{{ __('Option Name') }}</label>
                                    <input type="text" class="form-control" x-model="opt.name"
                                           :name="`options[${oi}][name]`">
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('Type') }}</label>
                                    <select class="form-control" x-model="opt.type" :name="`options[${oi}][type]`">
                                        <option value="single">{{ __('Single') }}</option>
                                        <option value="multiple">{{ __('Multiple') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('Required') }}</label><br>
                                    <input type="hidden" :name="`options[${oi}][is_required]`" value="0">
                                    <input type="checkbox" x-model="opt.is_required" :name="`options[${oi}][is_required]`" value="1">
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('Min Choices') }}</label>
                                    <input type="number" class="form-control" x-model="opt.min_choices"
                                           :name="`options[${oi}][min_choices]`" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('Max Choices') }}</label>
                                    <input type="number" class="form-control" x-model="opt.max_choices"
                                           :name="`options[${oi}][max_choices]`" min="0">
                                </div>
                            </div>

                            <div class="form-row mt-2">
                                <div class="col-md-2">
                                    <label>{{ __('Sort Order') }}</label>
                                    <input type="number" class="form-control" x-model="opt.sort_order"
                                           :name="`options[${oi}][sort_order]`" min="0">
                                </div>
                                <div class="col-md-2">
                                    <label>{{ __('Active') }}</label><br>
                                    <input type="hidden" :name="`options[${oi}][is_active]`" value="0">
                                    <input type="checkbox" x-model="opt.is_active" :name="`options[${oi}][is_active]`" value="1">
                                </div>
                                <div class="col-md-8 text-right">
                                    <button type="button" class="btn btn-sm btn-outline-danger mt-4" @click="removeOption(oi)">
                                        <i class="fa fa-trash mr-1"></i>{{ __('Remove Option') }}
                                    </button>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0">{{ __('Values') }}</h6>
                                    <button type="button" class="btn btn-xs btn-outline-secondary" @click="addValue(oi)">
                                        <i class="fa fa-plus mr-1"></i>{{ __('Add Value') }}
                                    </button>
                                </div>

                                <template x-for="(val, vi) in opt.values" :key="val._key">
                                    <div class="border rounded p-2 mt-2">
                                        <div class="form-row">
                                            <div class="col-md-4">
                                                <label>{{ __('Label') }}</label>
                                                <input type="text" class="form-control" x-model="val.label"
                                                       :name="`options[${oi}][values][${vi}][label]`">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{ __('Price Δ') }}</label>
                                                <input type="number" step="0.01" class="form-control" x-model="val.price_delta"
                                                       :name="`options[${oi}][values][${vi}][price_delta]`">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{ __('Default') }}</label><br>
                                                <input type="hidden" :name="`options[${oi}][values][${vi}][is_default]`" value="0">
                                                <input type="checkbox" x-model="val.is_default" :name="`options[${oi}][values][${vi}][is_default]`" value="1">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{ __('Sort') }}</label>
                                                <input type="number" class="form-control" x-model="val.sort_order"
                                                       :name="`options[${oi}][values][${vi}][sort_order]`" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label>{{ __('Active') }}</label><br>
                                                <input type="hidden" :name="`options[${oi}][values][${vi}][is_active]`" value="0">
                                                <input type="checkbox" x-model="val.is_active" :name="`options[${oi}][values][${vi}][is_active]`" value="1">
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                {{-- /OPTIONS --}}
            </div>
        </div>

        <div class="card-footer d-flex justify-content-end">
            <button type="submit" class="btn btn-primary custom-btn"><i class="fa fa-save mr-1"></i>{{ __('Save') }}</button>
        </div>
    </div>
</form>
@stop

@section('js')
{{-- Alpine.js (for the repeater) --}}
<script src="https://unpkg.com/alpinejs" defer></script>
<script>
(function(){
    function slugify(str){
        return (str||'').toString().normalize('NFD').replace(/[\u0300-\u036f]/g,'')
            .toLowerCase().replace(/[^a-z0-9]+/g,'-').replace(/^-+|-+$/g,'').replace(/--+/g,'-');
    }
    const nameInput=document.querySelector('input[name="name"]');
    const slugInput=document.getElementById('slug');
    const toggleBtn=document.getElementById('toggle-slug-edit');
    const syncBtn=document.getElementById('sync-slug');

    slugInput.readOnly=true;
    toggleBtn?.addEventListener('click', function(){
        const locked=slugInput.readOnly;
        slugInput.readOnly=!locked;
        this.innerHTML=locked?'<i class="fa fa-unlock"></i>':'<i class="fa fa-lock"></i>';
        if(!locked) slugInput.focus(); else slugInput.blur();
    });
    syncBtn?.addEventListener('click', function(){ slugInput.value=slugify(nameInput.value||''); });

    // Image preview
    const input = document.querySelector('input[name="image"]');
    const preview = document.getElementById('image-preview');
    input?.addEventListener('change', () => {
        const [file] = input.files || [];
        if (!file) { preview.style.display='none'; return; }
        const url = URL.createObjectURL(file);
        preview.src = url; preview.style.display = 'block';
    });
})();
</script>

<script>
function menuOptions() {
    // Prefill from old input on validation error (Create page)
    const initial = @json(old('options', []));
    // Normalize initial to ensure _key exists for Alpine
    const norm = (arr) => (arr||[]).map(o => ({
        id: o.id ?? null,
        name: o.name ?? '',
        type: o.type ?? 'single',
        is_required: !!(+o.is_required || o.is_required === true),
        min_choices: o.min_choices ?? 0,
        max_choices: o.max_choices ?? null,
        sort_order: o.sort_order ?? 0,
        is_active: !(o.is_active === '0' || o.is_active === 0) , // default true
        values: (o.values||[]).map(v => ({
            id: v.id ?? null,
            label: v.label ?? '',
            price_delta: v.price_delta ?? 0,
            is_default: !!(+v.is_default || v.is_default === true),
            sort_order: v.sort_order ?? 0,
            is_active: !(v.is_active === '0' || v.is_active === 0),
            _key: 'v'+Math.random().toString(36).slice(2),
        })),
        _key: 'o'+Math.random().toString(36).slice(2),
    }));

    return {
        options: norm(initial),
        addOption() {
            this.options.push({
                id: null,
                name:'', type:'single', is_required:false, min_choices:0, max_choices:null,
                sort_order:0, is_active:true, values:[],
                _key: 'o'+Math.random().toString(36).slice(2)
            });
        },
        removeOption(i){ this.options.splice(i,1); },
        addValue(i){
            this.options[i].values.push({
                id:null, label:'', price_delta:0, is_default:false, sort_order:0, is_active:true,
                _key: 'v'+Math.random().toString(36).slice(2)
            });
        }
    }
}
</script>
@stop
