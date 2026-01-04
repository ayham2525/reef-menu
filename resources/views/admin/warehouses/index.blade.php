@extends('adminlte::page')

@section('title', __('Warehouses'))

@section('content_header')
<div class="d-flex justify-content-between align-items-center">
    <h1 class="mb-0">
        <i class="fa fa-warehouse text-primary"></i> {{ __('Warehouses') }}
    </h1>

    <a href="{{ route('admin.warehouses.create') }}" class="btn btn-primary custom-btn">
        <i class="fa fa-plus mr-1"></i> {{ __('Add Warehouse') }}
    </a>
</div>
@stop

@section('content')

{{-- SUCCESS --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fa fa-check-circle mr-2"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
    </div>
@endif

<div class="card shadow-sm">

    <div class="card-header">
        <h3 class="card-title mb-0">
            <i class="fa fa-list-ul text-muted mr-1"></i> {{ __('Warehouses List') }}
        </h3>
    </div>

    <div class="card-body">
        {{-- SEARCH FILTER --}}
        <form id="filterForm" class="mb-3">
            <div class="form-row">

                <div class="col-md-3 mb-2">
                    <input type="text" name="search" id="search" class="form-control"
                           placeholder="{{ __('Search by name, code...') }}">
                </div>

                <div class="col-md-3 mb-2">
                    <select name="status" id="status" class="form-control">
                        <option value="all">{{ __('All Status') }}</option>
                        <option value="active">{{ __('Active') }}</option>
                        <option value="inactive">{{ __('Inactive') }}</option>
                    </select>
                </div>

            </div>
        </form>

        {{-- TABLE RENDER AJAX --}}
        <div id="table-container">
            @include('admin.warehouses.partials.table', ['warehouses' => $warehouses])
        </div>

    </div>
</div>
@stop

@section('js')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {

    const $search = $('#search');
    const $status = $('#status');
    const $container = $('#table-container');

    function fetchTable(page = 1) {
        const data = $('#filterForm').serialize();

        $.ajax({
            url: "{{ route('admin.warehouses.index') }}?page=" + page,
            data,
            headers: { 'X-Requested-With': 'XMLHttpRequest' },

            beforeSend: () => $container.css('opacity', .6),

            success: (html) => {
                $container.html(html);
                wirePagination();
                wireDelete();
            },

            complete: () => $container.css('opacity', 1),

            error: () => {
                $container.html(`
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i> {{ __('Could not load warehouses.') }}
                    </div>`);
            }
        });
    }

    // debounce search
    function debounce(fn, delay){
        let timer;
        return (...args) => { clearTimeout(timer); timer = setTimeout(() => fn(...args), delay); };
    }

    const searchTrigger = debounce(() => fetchTable(1), 300);

    $search.on('input', searchTrigger);
    $status.on('change', () => fetchTable(1));

    function wirePagination() {
        $container.find('.pagination a').off('click').on('click', function(e){
            e.preventDefault();
            const page = this.href.split('page=')[1];
            fetchTable(page);
        });
    }

    function wireDelete() {
        $('.delete-btn').off('click').on('click', function(e){
            e.preventDefault();

            const form = $(this).closest('form');

            Swal.fire({
                title: "{{ __('Are you sure?') }}",
                text: "{{ __('This action cannot be undone.') }}",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "{{ __('Yes, delete!') }}",
                cancelButtonText: "{{ __('Cancel') }}",
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary ml-2'
                },
                buttonsStyling: false
            }).then(result => {
                if (result.isConfirmed) form.submit();
            });
        });
    }

    // initial load
    wirePagination();
    wireDelete();

})();
</script>
@stop
