{{-- resources/views/admin/menu_categories/show.blade.php --}}
@extends('adminlte::page')

@section('title', __('Menu Categories') . ' - ' . __('Details'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-folder-open text-primary mr-2"></i>{{ __('Category Details') }}
            <small class="ml-2 text-muted">/{{ $category->slug }}</small>
        </h1>
        <div class="btn-group">
            <a href="{{ route('admin.menu-categories.index') }}" class="btn btn-secondary mr-1">
                <i class="fa fa-arrow-left mr-1"></i>{{ __('Back to List') }}
            </a>
            <a href="{{ route('admin.menu-categories.edit', $category) }}" class="btn btn-primary mr-1">
                <i class="fa fa-edit mr-1"></i>{{ __('Edit') }}
            </a>
            <form action="{{ route('admin.menu-categories.destroy', $category) }}" method="POST" class="d-inline">
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
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0"><i class="fa fa-info-circle mr-2 text-primary"></i>{{ __('Summary') }}</h3>
                    <div>
                        @if($category->is_active)
                            <span class="badge badge-success"><i class="fa fa-check mr-1"></i>{{ __('Active') }}</span>
                        @else
                            <span class="badge badge-secondary"><i class="fa fa-ban mr-1"></i>{{ __('Inactive') }}</span>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4"><i class="fa fa-tag mr-1 text-muted"></i>{{ __('Name') }}</dt>
                        <dd class="col-sm-8">{{ $category->name }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-link mr-1 text-muted"></i>{{ __('Slug') }}</dt>
                        <dd class="col-sm-8">/{{ $category->slug }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-hashtag mr-1 text-muted"></i>{{ __('Code') }}</dt>
                        <dd class="col-sm-8">{{ $category->code ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-sitemap mr-1 text-muted"></i>{{ __('Parent') }}</dt>
                        <dd class="col-sm-8">{{ optional($category->parent)->name ?? '—' }}</dd>

                        <dt class="col-sm-4"><i class="fa fa-sort-numeric-down mr-1 text-muted"></i>{{ __('Sort Order') }}</dt>
                        <dd class="col-sm-8">{{ $category->sort_order }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fa fa-sticky-note mr-2 text-primary"></i>{{ __('Description') }}</h3>
                </div>
                <div class="card-body">
                    @if($category->description)
                        <p class="mb-0" style="white-space: pre-line;">{{ $category->description }}</p>
                    @else
                        <span class="text-muted"><i class="fa fa-info-circle mr-1"></i>{{ __('No description.') }}</span>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><i class="fa fa-list mr-2 text-primary"></i>{{ __('Children & Items') }}</h3>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2"><strong>{{ __('Subcategories') }}:</strong> {{ $category->children->count() }}</li>
                        <li class="mb-2"><strong>{{ __('Items') }}:</strong> {{ $category->items->count() }}</li>
                        <li class="mb-2"><strong>{{ __('Created At') }}:</strong> {{ optional($category->created_at)->format('Y-m-d H:i') }}</li>
                        <li class="mb-2"><strong>{{ __('Updated At') }}:</strong> {{ optional($category->updated_at)->format('Y-m-d H:i') }}</li>
                        <li class="mb-2"><strong>{{ __('Created By') }}:</strong> {{ $category->created_by ?? '—' }}</li>
                        <li><strong>{{ __('Updated By') }}:</strong> {{ $category->updated_by ?? '—' }}</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            document.querySelectorAll('.js-delete-link').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    const form = this.closest('form');
                    Swal.fire({
                        title: "{{ __('Delete this category?') }}",
                        text: "{{ __('This action cannot be undone.') }}",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: "{{ __('Yes, delete it') }}",
                        cancelButtonText: "{{ __('Cancel') }}",
                        customClass: { confirmButton: 'btn btn-danger mr-2', cancelButton: 'btn btn-secondary' },
                        buttonsStyling: false
                    }).then((result) => { if (result.isConfirmed && form) form.submit(); });
                });
            });
        })();
    </script>
@stop
