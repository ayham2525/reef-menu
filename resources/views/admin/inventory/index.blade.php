@extends('adminlte::page')

@section('title', __('Inventory Stock'))

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css">
@stop

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-boxes text-primary"></i> {{ __('Inventory Stock') }}
    </h1>
</div>
@stop

@section('content')

<div class="card shadow-sm">

    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fa fa-list-ul text-muted mr-1"></i> {{ __('Stock Overview') }}
        </h3>
    </div>

    <div class="card-body">

        {{-- Filters --}}
        <form id="filterForm" class="mb-3">
            <div class="form-row">

                {{-- Search --}}
                <div class="col-md-4 mb-2">
                    <input type="text"
                           name="search"
                           id="search"
                           class="form-control"
                           placeholder="{{ __('Search item...') }}"
                           value="{{ request('search') }}">
                </div>

                {{-- Warehouse Filter --}}
                <div class="col-md-4 mb-2">
                    <select name="warehouse_id"
                            id="warehouse_id"
                            class="select2bs4"
                            data-placeholder="{{ __('All Warehouses') }}">
                        <option value="">{{ __('All Warehouses') }}</option>
                        @foreach ($warehouses as $w)
                            <option value="{{ $w->id }}" {{ request('warehouse_id') == $w->id ? 'selected' : '' }}>
                                {{ $w->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>
        </form>

        {{-- Table Container (AJAX updated) --}}
        <div id="table-container">
            @include('admin.inventory.partials.table', ['stocks' => $stocks])
        </div>

    </div>
</div>

@stop


@section('js')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
(function () {
    const $search = $('#search');
    const $warehouse = $('#warehouse_id');
    const $container = $('#table-container');

    // Select2
    $warehouse.select2({
        theme: 'bootstrap4',
        width: '100%',
        allowClear: true,
        placeholder: "{{ __('All Warehouses') }}"
    });

    // AJAX Fetch
    function fetchTable(page = 1) {
        const data = $('#filterForm').serialize();

        $.ajax({
            url: "{{ route('admin.inventory.index') }}?page=" + page,
            data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            beforeSend: () => $container.css('opacity', .6),
            success: html => {
                $container.html(html);
                wirePagination();
            },
            complete: () => $container.css('opacity', 1),
            error: () => {
                $container.html(`<div class="alert alert-warning">
                    <i class="fa fa-exclamation-circle"></i>
                    {{ __('Error loading data.') }}
                </div>`);
            }
        });
    }

    // Pagination binding
    function wirePagination() {
        $('#table-container .pagination a').on('click', function(e){
            e.preventDefault();
            const page = this.href.split('page=')[1];
            fetchTable(page);
        });
    }

    // Debounce
    function debounce(fn, ms){
        let t;
        return () => {
            clearTimeout(t);
            t = setTimeout(fn, ms);
        };
    }

    const doSearch = debounce(() => fetchTable(1), 300);

    // Filter events
    $search.on('input', doSearch);
    $warehouse.on('change', () => fetchTable(1));

    // First load pagination events
    wirePagination();
})();
</script>
@stop
