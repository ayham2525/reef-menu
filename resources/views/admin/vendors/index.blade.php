@extends('adminlte::page')

@section('title', __('Vendors'))

{{-- Page-specific CSS --}}
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
.select2-container--bootstrap4 .select2-selection--single{height:calc(2.25rem + 2px)}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{line-height:2.25rem}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow{height:calc(2.25rem + 2px)}
</style>
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-truck-loading text-primary"></i> {{ __('Vendors') }}
    </h1>

    <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary custom-btn">
        <i class="fa fa-plus mr-1"></i> {{ __('Add Vendor') }}
    </a>
</div>
@stop

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fa fa-list-ul"></i> {{ __('Vendors List') }}
        </h3>
    </div>

    <div class="card-body">
        {{-- Filters --}}
        <form id="filterForm" class="mb-3">
            <div class="form-row">

                {{-- Search --}}
                <div class="col-md-3 mb-2">
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="{{ __('Search by name, phone, email...') }}"
                           value="{{ request('search') }}">
                </div>

                {{-- Status --}}
                <div class="col-md-3 mb-2">
                    <select name="status" id="status" class="form-control">
                        <option value="all">{{ __('All Statuses') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

            </div>
        </form>

        {{-- Table container (AJAX) --}}
        <div id="table-container">
            @include('admin.vendors.partials.table', ['vendors' => $vendors])
        </div>
    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
(function () {

    const $search    = $('#search');
    const $status    = $('#status');
    const $container = $('#table-container');

    function fetchTable(page = 1) {
        const data = $('#filterForm').serialize();

        $.ajax({
            url: "{{ route('admin.vendors.index') }}" + "?page=" + page,
            data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            beforeSend: () => $container.css('opacity', .6),

            success(html) {
                $container.html(html);
                wirePagination();
                wireDelete();
            },

            complete() { $container.css('opacity', 1); },

            error() {
                $container.html(`
                    <div class="alert alert-warning mb-0">
                        <i class="fa fa-exclamation-triangle mr-1"></i>
                        {{ __('Could not load data. Please try again.') }}
                    </div>
                `);
            }
        });
    }

    function debounce(fn, delay) {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), delay);
        };
    }

    const onSearch = debounce(() => fetchTable(1), 300);

    $search.on('input', onSearch).on('keydown', e => {
        if (e.key === 'Enter') {
            e.preventDefault();
            fetchTable(1);
        }
        if (e.key === 'Escape') {
            $search.val('');
            fetchTable(1);
        }
    });

    $status.on('change', () => fetchTable(1));

    function wirePagination() {
        $container.find('.pagination a').off('click').on('click', function(e){
            e.preventDefault();
            const page = $(this).attr('href').split('page=')[1];
            fetchTable(page);
        });
    }

    function wireDelete() {
        $container.find('.js-delete-link').off('click').on('click', function(e){
            e.preventDefault();
            const form = $(this).closest('form');

            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This action cannot be undone!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
                customClass: {
                    confirmButton: 'btn btn-danger mr-2',
                    cancelButton: 'btn btn-secondary'
                },
                buttonsStyling: false
            }).then((res) => {
                if (res.isConfirmed) form.submit();
            });
        });
    }

    wirePagination();
    wireDelete();

})();
</script>
@stop
