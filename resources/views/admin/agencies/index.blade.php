@extends('adminlte::page')

@section('title', __('Agencies'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-building text-primary"></i> {{ __('Agencies') }}
        </h1>
        <a href="{{ route('admin.agencies.create') }}" class="btn btn-primary custom-btn">
            <i class="fa fa-plus mr-1"></i> {{ __('New Agency') }}
        </a>
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

    {{-- Status pills --}}
    <ul class="nav nav-pills mb-3" id="status-pills">
        <li class="nav-item custom-nav-item">
            <a href="{{ route('admin.agencies.index', array_filter(['search' => request('search'), 'status' => 'all'])) }}"
               class="nav-link {{ ($status ?? 'all') === 'all' ? 'active custom-btn' : '' }}"
               data-status="all">
                <i class="fa fa-list-ul mr-1"></i> {{ __('All') }}
            </a>
        </li>
        <li class="nav-item custom-nav-item">
            <a href="{{ route('admin.agencies.index', array_filter(['search' => request('search'), 'status' => 'active'])) }}"
               class="nav-link {{ ($status ?? 'all') === 'active' ? 'active custom-btn' : '' }}"
               data-status="active">
                <i class="fa fa-check-circle"></i> {{ __('Active') }}
                <span class="badge badge-light ml-1">{{ $totalActive ?? 0 }}</span>
            </a>
        </li>
        <li class="nav-item custom-nav-item">
            <a href="{{ route('admin.agencies.index', array_filter(['search' => request('search'), 'status' => 'inactive'])) }}"
               class="nav-link {{ ($status ?? 'all') === 'inactive' ? 'active custom-btn' : '' }}"
               data-status="inactive">
                <i class="fa fa-ban"></i> {{ __('Inactive') }}
                <span class="badge badge-light ml-1">{{ $totalInactive ?? 0 }}</span>
            </a>
        </li>
    </ul>

    {{-- Search --}}
    <div class="mb-3" style="max-width: 480px;">
        <div class="input-group">
            <input type="text" id="agency-search" name="search" value="{{ request('search') }}" class="form-control"
                   placeholder="{{ __('Search by name / code / license / email / phone') }}" autocomplete="off">
            <div class="input-group-append">
                <span class="input-group-text"><i class="fa fa-search"></i></span>
            </div>
        </div>
        <small class="form-text text-muted">{{ __('Type to search…') }}</small>
    </div>

    {{-- Table container replaced via AJAX --}}
    <div id="agencies-table">
        @include('admin.agencies.partials.table', ['agencies' => $agencies])
    </div>
@stop

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function () {
    const input   = document.getElementById('agency-search');
    const tableEl = document.getElementById('agencies-table');
    const pills   = document.getElementById('status-pills');
    let currentStatus = "{{ $status ?? 'all' }}";

    function debounce(fn, delay) { let t; return (...a)=>{ clearTimeout(t); t=setTimeout(()=>fn(...a),delay); }; }

    function makeUrl(pageUrl = null) {
        const base = pageUrl ? new URL(pageUrl, window.location.origin)
                             : new URL("{{ route('admin.agencies.index') }}", window.location.origin);
        const q = (input?.value || '').trim();
        q ? base.searchParams.set('search', q) : base.searchParams.delete('search');
        currentStatus && currentStatus !== 'all' ? base.searchParams.set('status', currentStatus)
                                                 : base.searchParams.delete('status');
        return base.toString();
    }

    async function loadTable(url) {
        try {
            tableEl.style.opacity = '0.6';
            const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            if (!res.ok) throw 0;
            tableEl.innerHTML = await res.text();
        } catch (e) {
            tableEl.innerHTML = `<div class="alert alert-warning mb-0">
                <i class="fa fa-exclamation-triangle mr-1"></i>{{ __('Could not load data. Please try again.') }}
            </div>`;
        } finally {
            tableEl.style.opacity = '1';
            wirePagination();
            wireDeleteConfirm(); // rebind after AJAX replace
        }
    }

    const onType = debounce(function () {
        const url = makeUrl();
        loadTable(url);
        window.history.replaceState({}, '', url);
    }, 300);

    if (!window.__agenciesBound) {
        window.__agenciesBound = true;
        input.addEventListener('input', onType);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); loadTable(makeUrl()); }
            if (e.key === 'Escape') { input.value=''; loadTable(makeUrl()); }
        });
    }

    function wirePagination() {
        const links = tableEl.querySelectorAll('.pagination a');
        links.forEach(a => {
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                const targetUrl = this.getAttribute('href');
                const finalUrl = makeUrl(targetUrl);
                loadTable(finalUrl);
                window.history.replaceState({}, '', finalUrl);
            });
        });
    }

    // SweetAlert2 delete confirm
    function wireDeleteConfirm() {
        const deleteLinks = tableEl.querySelectorAll('.js-delete-link');
        deleteLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const form = this.closest('form');
                Swal.fire({
                    title: "{{ __('Delete this agency?') }}",
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
                    if (result.isConfirmed && form) {
                        form.submit();
                    }
                });
            }, { once: false });
        });
    }

    // Initial binds
    wirePagination();
    wireDeleteConfirm();

    // Status pill clicks (AJAX)
    if (pills) {
        pills.querySelectorAll('a.nav-link').forEach(a => {
            a.addEventListener('click', function (ev) {
                ev.preventDefault();
                currentStatus = this.dataset.status || 'all';
                pills.querySelectorAll('a.nav-link').forEach(el => el.classList.remove('custom-btn','active'));
                this.classList.add('custom-btn','active');
                const url = makeUrl();
                loadTable(url);
                window.history.replaceState({}, '', url);
            });
        });
    }
})();
</script>
@stop
