@extends('adminlte::page')

@section('title', __('Menu Items') . ' - ' . __('Details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-id-badge text-primary mr-2"></i>{{ __('Item Details') }}
            <small class="ml-2 text-muted">/{{ $item->slug }}</small>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.menu-items.index') }}" class="btn btn-secondary mr-1">
                <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.menu-items.edit', $item) }}" class="btn btn-primary mr-1">
                <i class="fa fa-edit mr-1"></i>{{ __('Edit') }}
            </a>
            <form action="{{ route('admin.menu-items.destroy', $item) }}" method="POST" class="d-inline">
                @csrf @method('DELETE')
                <button type="button" class="btn btn-danger js-delete-link">
                    <i class="fa fa-trash mr-1"></i>{{ __('Delete') }}
                </button>
            </form>
        </div>
    </div>
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa fa-check-circle mr-2"></i>
            <span>{{ session('success') }}</span>
            <button type="button" class="close ml-auto" data-dismiss="alert" aria-label="{{ __('Close') }}">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            {{-- Summary --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-utensils mr-2 text-primary"></i>{{ $item->name }}
                    </h3>
                    <div>
                        @if ($item->is_active)
                            <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-secondary"><i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                        @endif
                        @if ($item->is_available)
                            <span class="badge badge-info"><i class="fa fa-store mr-1"></i>{{ __('Available') }}</span>
                        @endif
                        @if ($item->is_featured)
                            <span class="badge badge-warning"><i class="fa fa-star mr-1"></i>{{ __('Featured') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fa fa-layer-group mr-1 text-muted"></i>{{ __('Category') }}</dt>
                        <dd class="col-sm-8">{{ optional($item->category)->name ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-hashtag mr-1 text-muted"></i>{{ __('SKU') }}</dt>
                        <dd class="col-sm-8">{{ $item->sku ?: '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-dollar-sign mr-1 text-muted"></i>{{ __('Price') }}</dt>
                        <dd class="col-sm-8">{{ number_format($item->price, 2) }} {{ $item->currency }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-clock mr-1 text-muted"></i>{{ __('Prep Time') }}</dt>
                        <dd class="col-sm-8">
                            {{ $item->prep_time_minutes ? $item->prep_time_minutes . ' ' . __('min') : '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-fire mr-1 text-muted"></i>{{ __('Calories') }}</dt>
                        <dd class="col-sm-8">{{ $item->calories ?: '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-tags mr-1 text-muted"></i>{{ __('Tags') }}</dt>
                        <dd class="col-sm-8">
                            @if ($item->tags && is_array($item->tags))
                                @foreach ($item->tags as $t)
                                    <span class="badge badge-light">{{ $t }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </dd>

                        <dt class="col-sm-4"><i
                                class="fa fa-exclamation-triangle mr-1 text-muted"></i>{{ __('Allergens') }}</dt>
                        <dd class="col-sm-8">
                            @if ($item->allergens && is_array($item->allergens))
                                @foreach ($item->allergens as $t)
                                    <span class="badge badge-danger">{{ $t }}</span>
                                @endforeach
                            @else
                                —
                            @endif
                        </dd>
                    </dl>
                </div>
            </div>

            {{-- Description --}}
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-sticky-note mr-2 text-primary"></i>{{ __('Description') }}
                    </h3>
                </div>
                <div class="card-body">
                    @if ($item->description)
                        <p class="mb-0" style="white-space: pre-line;">{{ $item->description }}</p>
                    @else
                        <span class="text-muted"><i class="fa fa-info-circle mr-1"></i>{{ __('No description.') }}</span>
                    @endif
                </div>
            </div>

            {{-- Options & Variants --}}
            <div class="card shadow-sm">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0">
                        <i class="fa fa-sliders-h mr-2 text-primary"></i>{{ __('Options & Variants') }}
                    </h3>
                    @if ($item->options && $item->options->count())
                        <span class="badge badge-light">{{ $item->options->count() }} {{ __('option(s)') }}</span>
                    @endif
                </div>
                <div class="card-body">
                    @if (!$item->options || $item->options->isEmpty())
                        <span class="text-muted"><i
                                class="fa fa-info-circle mr-1"></i>{{ __('No options configured.') }}</span>
                    @else
                        <div class="accordion" id="itemOptionsAccordion">
                            @foreach ($item->options->sortBy(['sort_order', 'id']) as $optIndex => $opt)
                                @php
                                    $optId = 'opt_' . $opt->id ?? 'opt_new_' . $loop->index;
                                    $typeLabel = $opt->type === 'multiple' ? __('Multiple') : __('Single');
                                    $reqLabel = $opt->is_required ? __('Required') : __('Optional');
                                    $bounds = [];
                                    if (!is_null($opt->min_choices)) {
                                        $bounds[] = __('Min') . ': ' . $opt->min_choices;
                                    }
                                    if (!is_null($opt->max_choices)) {
                                        $bounds[] = __('Max') . ': ' . $opt->max_choices;
                                    }
                                    $boundsText = $bounds ? ' (' . implode(', ', $bounds) . ')' : '';
                                @endphp

                                <div class="card mb-2">
                                    <div class="card-header p-2" id="heading-{{ $optId }}">
                                        <h5 class="mb-0 d-flex align-items-center">
                                            <button class="btn btn-link text-left" type="button" data-toggle="collapse"
                                                data-target="#collapse-{{ $optId }}"
                                                aria-expanded="{{ $optIndex === 0 ? 'true' : 'false' }}"
                                                aria-controls="collapse-{{ $optId }}">
                                                <strong>{{ $opt->name }}</strong>
                                            </button>
                                            <div class="ml-auto">
                                                <span
                                                    class="badge badge-{{ $opt->type === 'multiple' ? 'info' : 'primary' }}">
                                                    <i class="fa fa-list mr-1"></i>{{ $typeLabel }}
                                                </span>
                                                <span
                                                    class="badge badge-{{ $opt->is_required ? 'danger' : 'secondary' }} ml-1">
                                                    <i class="fa fa-asterisk mr-1"></i>{{ $reqLabel }}
                                                </span>
                                                @if ($boundsText)
                                                    <span class="badge badge-light ml-1">
                                                        <i class="fa fa-balance-scale mr-1"></i>{{ $boundsText }}
                                                    </span>
                                                @endif
                                                @if (!$opt->is_active)
                                                    <span class="badge badge-secondary ml-1"><i
                                                            class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                                                @endif
                                            </div>
                                        </h5>
                                    </div>

                                    <div id="collapse-{{ $optId }}"
                                        class="collapse {{ $optIndex === 0 ? 'show' : '' }}"
                                        aria-labelledby="heading-{{ $optId }}"
                                        data-parent="#itemOptionsAccordion">
                                        <div class="card-body py-2">
                                            @php $values = $opt->values->sortBy(['sort_order','id']); @endphp

                                            @if ($values->isEmpty())
                                                <span class="text-muted"><i
                                                        class="fa fa-info-circle mr-1"></i>{{ __('No values for this option.') }}</span>
                                            @else
                                                <ul class="list-group list-group-flush">
                                                    @foreach ($values as $val)
                                                        <li class="list-group-item d-flex align-items-center px-0">
                                                            <div class="flex-grow-1">
                                                                <strong>{{ $val->label }}</strong>
                                                                @if ($val->is_default)
                                                                    <span class="badge badge-success ml-1">
                                                                        <i
                                                                            class="fa fa-check mr-1"></i>{{ __('Default') }}
                                                                    </span>
                                                                @endif
                                                                @if (!$val->is_active)
                                                                    <span class="badge badge-secondary ml-1">
                                                                        <i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}
                                                                    </span>
                                                                @endif
                                                                @if (!is_null($val->price_delta) && (float) $val->price_delta != 0)
                                                                    <small class="text-muted ml-2">
                                                                        {{ $val->price_delta > 0 ? '+' : '' }}{{ number_format($val->price_delta, 2) }}
                                                                        {{ $item->currency }}
                                                                    </small>
                                                                @endif
                                                            </div>
                                                            <span class="text-muted small ml-3">
                                                                {{ __('Sort') }}: {{ (int) $val->sort_order }}
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right column: image + meta --}}
        <div class="col-md-4">
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h3 class="card-title mb-0"><i class="fa fa-image mr-2 text-primary"></i>{{ __('Primary Image') }}
                    </h3>
                    @if ($item->images && $item->images->count())
                        <span class="badge badge-light">{{ $item->images->count() }}</span>
                    @endif
                </div>
                <div class="card-body text-center">
                    @if ($item->primary_image_url)
                        <img src="{{ $item->primary_image_url }}" class="img-fluid rounded" alt="">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center"
                            style="width:100%;height:180px;border:1px dashed #ddd;">
                            <i class="fa fa-image text-muted"></i>
                        </div>
                    @endif

                    {{-- Optional gallery thumbnails (if you store multiple images) --}}
                    @if ($item->images && $item->images->count() > 1)
                        <hr>
                        <div class="row">
                            @foreach ($item->images->sortBy(['sort_order', 'id'])->take(6) as $g)
                                @php $thumb = method_exists($g,'getUrlAttribute') ? $g->url : (\Storage::disk($g->disk ?? 'public')->url($g->path)); @endphp
                                @if (!$g->is_primary)
                                    <div class="col-4 mb-2">
                                        <img src="{{ $thumb }}" class="img-fluid rounded"
                                            alt="{{ $g->alt_text }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fa fa-info-circle mr-2 text-primary"></i>{{ __('Meta') }}
                    </h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>{{ __('Sort Order') }}:</strong> {{ $item->sort_order }}</li>
                        <li class="mb-2"><strong>{{ __('Created At') }}:</strong>
                            {{ optional($item->created_at)->format('Y-m-d H:i') }}</li>
                        <li class="mb-2"><strong>{{ __('Updated At') }}:</strong>
                            {{ optional($item->updated_at)->format('Y-m-d H:i') }}</li>
                        <li class="mb-2">
                            <strong>{{ __('Created By') }}:</strong>
                            {{ $item->creator?->name ?? '—' }}
                        </li>

                        <li class="mb-2">
                            <strong>{{ __('Updated By') }}:</strong>
                            {{ $item->updater?->name ?? '—' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function() {
            document.querySelectorAll('.js-delete-link').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('Delete this item?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete it') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        customClass: {
                            confirmButton: 'btn btn-danger mr-2',
                            cancelButton: 'btn btn-secondary'
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed && form) form.submit();
                    });
                });
            });
        })();
    </script>
@stop
