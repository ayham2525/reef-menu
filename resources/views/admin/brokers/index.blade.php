@extends('adminlte::page')

@section('title', __('Brokers'))

{{-- Page-specific CSS (Select2 + theme + tiny height alignment) --}}
@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
<style>
/* Ensure Select2 single matches .form-control height in Bootstrap 4 */
.select2-container--bootstrap4 .select2-selection--single{height:calc(2.25rem + 2px)}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__rendered{line-height:2.25rem}
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow{height:calc(2.25rem + 2px)}
</style>
@stop

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-user-tie text-primary"></i> {{ __('Brokers') }}
        </h1>
        <a href="{{ route('admin.brokers.create') }}" class="btn btn-primary custom-btn">
            <i class="fa fa-plus mr-1"></i> {{ __('Add Broker') }}
        </a>
    </div>
@stop

@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title mb-0"><i class="fa fa-list-ul"></i> {{ __('Brokers List') }}</h3>
        </div>

        <div class="card-body">
            {{-- Filters --}}
            <form id="filterForm" class="mb-3">
                <div class="form-row">
                    <div class="col-md-3 mb-2">
                        <input type="text"
                               name="search"
                               id="search"
                               class="form-control"
                               placeholder="{{ __('Search by name, email, phone...') }}"
                               value="{{ request('search') }}">
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="agency_id"
                                id="agency_id"
                                class="select2bs4"
                                data-placeholder="{{ __('All Agencies') }}">
                            <option value="">{{ __('All Agencies') }}</option>
                            @foreach($agencies as $agency)
                                <option value="{{ $agency->id }}" {{ ($selectedAgency ?? null) == $agency->id ? 'selected' : '' }}>
                                    {{ $agency->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-2">
                        <select name="status" id="status" class="form-control">
                            <option value="all" {{ ($status ?? 'all') == 'all' ? 'selected' : '' }}>{{ __('All Statuses') }}</option>
                            <option value="active" {{ ($status ?? 'all') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                            <option value="inactive" {{ ($status ?? 'all') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                        </select>
                    </div>
                </div>
            </form>

            {{-- Table container (replaced via AJAX) --}}
            <div id="table-container">
                @include('admin.brokers.partials.table', ['brokers' => $brokers])
            </div>
        </div>
    </div>
@stop

{{-- Page-specific JS (Select2, SweetAlert, and AJAX handlers) --}}
@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const $search    = $('#search');
    const $status    = $('#status');
    const $agency    = $('#agency_id');
    const $container = $('#table-container');

    // Initialize Select2 (Bootstrap 4 theme)
    $agency.select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        placeholder: $agency.data('placeholder') || "{{ __('All Agencies') }}",
        dropdownParent: $('#filterForm')
    });

    function fetchTable(page = 1) {
        const data = $('#filterForm').serialize();
        $.ajax({
            url: "{{ route('admin.brokers.index') }}" + "?page=" + page,
            data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            beforeSend: () => $container.css('opacity', .6),
            success(html) { $container.html(html); wirePagination(); wireDelete(); },
            complete()    { $container.css('opacity', 1); },
            error() {
                $container.html(`<div class="alert alert-warning mb-0">
                    <i class="fa fa-exclamation-triangle mr-1"></i>{{ __('Could not load data. Please try again.') }}
                </div>`);
            }
        });
    }

    function debounce(fn, wait){ let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),wait); }; }
    const onSearch = debounce(() => fetchTable(1), 300);

    $search.on('input', onSearch)
           .on('keydown', e => { if (e.key==='Enter'){ e.preventDefault(); fetchTable(1);} if (e.key==='Escape'){ $search.val(''); fetchTable(1);} });
    $status.on('change', () => fetchTable(1));
    $agency.on('change', () => fetchTable(1));

    function wirePagination() {
        $container.find('.pagination a').off('click').on('click', function(e){
            e.preventDefault();
            const page = (this.getAttribute('href') || '').split('page=')[1] || 1;
            fetchTable(page);
        });
    }

    function wireDelete() {
        $container.find('.delete-btn').off('click').on('click', function(e){
            e.preventDefault();
            const url = this.getAttribute('href');
            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This action cannot be undone!') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete it!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
                customClass: { confirmButton: 'btn btn-danger mr-2', cancelButton: 'btn btn-secondary' },
                buttonsStyling: false
            }).then((result) => { if (result.isConfirmed && url) window.location.href = url; });
        });
    }

    // Initial binds (for first render)
    wirePagination(); wireDelete();
})();
</script>
@stop
