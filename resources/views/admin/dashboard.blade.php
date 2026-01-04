@extends('adminlte::page')

@section('title', __('Dashboard'))

@section('content_header')
    <h1><i class="fa fa-chart-line text-primary"></i> {{ __('Orders Dashboard') }}</h1>
@stop

@section('content')

    {{-- Filter Bar --}}
    <div class="card mb-4">
        <div class="card-header">
            <i class="fa fa-filter mr-1"></i> {{ __('Filters') }}
        </div>
        <div class="card-body">
            <form method="GET" action="{{ url()->current() }}" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="from_date" class="mr-2">{{ __('From') }}</label>
                    <input
                        type="date"
                        id="from_date"
                        name="from_date"
                        class="form-control"
                        value="{{ old('from_date', isset($fromDate) ? $fromDate->format('Y-m-d') : '') }}"
                    >
                </div>

                <div class="form-group mr-3 mb-2">
                    <label for="to_date" class="mr-2">{{ __('To') }}</label>
                    <input
                        type="date"
                        id="to_date"
                        name="to_date"
                        class="form-control"
                        value="{{ old('to_date', isset($toDate) ? $toDate->format('Y-m-d') : '') }}"
                    >
                </div>

                <button type="submit" class="btn btn-primary mb-2 mr-2">
                    <i class="fa fa-search mr-1"></i> {{ __('Apply') }}
                </button>

                <a href="{{ url()->current() }}" class="btn btn-secondary mb-2">
                    <i class="fa fa-undo mr-1"></i> {{ __('Reset') }}
                </a>
            </form>
        </div>
    </div>

    {{-- KPI BOXES --}}
    <div class="row">
        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="{{ $totalOrders }}"
                text="{{ __('Total Orders') }}"
                theme="primary"
                icon="fas fa-shopping-cart"
            />
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="{{ $completedOrders }}"
                text="{{ __('Completed') }}"
                theme="success"
                icon="fas fa-check"
            />
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="{{ $pendingOrders }}"
                text="{{ __('Pending') }}"
                theme="warning"
                icon="fas fa-clock"
            />
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="{{ $cancelledOrders }}"
                text="{{ __('Cancelled') }}"
                theme="danger"
                icon="fas fa-times"
            />
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="AED {{ number_format($totalRevenue, 2) }}"
                text="{{ __('Total Revenue') }}"
                theme="info"
                icon="fas fa-dollar-sign"
            />
        </div>

        <div class="col-md-2 col-sm-6 mb-3">
            <x-adminlte-small-box
                title="AED {{ number_format($avgOrderValue, 2) }}"
                text="{{ __('Avg Order Value') }}"
                theme="secondary"
                icon="fas fa-chart-area"
            />
        </div>
    </div>

    {{-- Charts Row 1 --}}
    <div class="row">
        {{-- Orders per Day --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fa fa-chart-line mr-1"></i>
                    {{ __('Orders per Day (Selected Period)') }}
                </div>
                <div class="card-body">
                    <canvas id="chartOrdersDaily" height="140"></canvas>
                </div>
            </div>
        </div>

        {{-- Revenue per Day --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fa fa-money-bill-wave mr-1"></i>
                    {{ __('Revenue per Day (Completed Orders)') }}
                </div>
                <div class="card-body">
                    <canvas id="chartRevenueDaily" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row 2 --}}
    <div class="row">
        {{-- Top 5 Items --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fa fa-fire mr-1"></i>
                    {{ __('Top 5 Selling Items (Selected Period)') }}
                </div>
                <div class="card-body">
                    <canvas id="chartTopItems" height="140"></canvas>
                </div>
            </div>
        </div>

        {{-- Orders by Status --}}
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <i class="fa fa-chart-pie mr-1"></i>
                    {{ __('Orders by Status') }}
                </div>
                <div class="card-body">
                    <canvas id="chartOrdersStatus" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Latest Orders (full width) --}}
   <div class="card">
    <div class="card-header">
        <i class="fa fa-clock mr-1"></i> {{ __('Latest Orders') }}
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>{{ __('Order #') }}</th>
                        <th>{{ 'Name' }}</th>
                        <th>{{ __('Customer') }}</th>
                        <th>{{ __('Total (AED)') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th class="text-right">{{ __('Date') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 0; @endphp

                    @forelse($latestOrders as $order)
                        @php $i++; @endphp
                        <tr class="clickable-row"
                            data-href="{{ route('admin.orders.show', $order->id) }}"
                            style="cursor: pointer;">
                            <td>#{{ $i }}</td>
                            <td>
                                @if(!empty($order->agency_name))
                                    {{ $order->agency_name }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>{{ optional($order->user)->name ?? __('Guest') }}</td>
                            <td>{{ number_format($order->total_amount, 2) }}</td>
                            <td>
                                @php
                                    $badgeClass = match ($order->status) {
                                        'completed' => 'success',
                                        'pending'   => 'warning',
                                        'cancelled' => 'danger',
                                        default     => 'secondary',
                                    };
                                @endphp
                                <span class="badge badge-{{ $badgeClass }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td class="text-right">{{ $order->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted p-4">
                                {{ __('No recent orders') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>


@stop

@push('js')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Make rows clickable
            document.querySelectorAll('.clickable-row').forEach(row => {
                row.addEventListener('click', function () {
                    window.location = this.dataset.href;
                });
            });
        });
    </script>

    <script>
        const ordersPerDay   = @json($ordersPerDay);
        const revenuePerDay  = @json($revenuePerDay);
        const topItems       = @json($topItems);
        const ordersByStatus = @json($ordersByStatus);

        // Orders per Day Line Chart
        const ctxDaily = document.getElementById('chartOrdersDaily').getContext('2d');
        new Chart(ctxDaily, {
            type: 'line',
            data: {
                labels: ordersPerDay.map(i => i.date),
                datasets: [{
                    label: '{{ __("Orders") }}',
                    data: ordersPerDay.map(i => i.value),
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Revenue per Day Line Chart
        const ctxRevenue = document.getElementById('chartRevenueDaily').getContext('2d');
        new Chart(ctxRevenue, {
            type: 'line',
            data: {
                labels: revenuePerDay.map(i => i.date),
                datasets: [{
                    label: '{{ __("Revenue (AED)") }}',
                    data: revenuePerDay.map(i => i.value),
                    tension: 0.3,
                    fill: false
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Top 5 Items Bar Chart
        const ctxTopItems = document.getElementById('chartTopItems').getContext('2d');
        new Chart(ctxTopItems, {
            type: 'bar',
            data: {
                labels: topItems.map(i => i.label),
                datasets: [{
                    label: '{{ __("Quantity Sold") }}',
                    data: topItems.map(i => i.value)
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });

        // Orders by Status Doughnut Chart
        const ctxStatus = document.getElementById('chartOrdersStatus').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: ordersByStatus.map(i => i.label),
                datasets: [{
                    data: ordersByStatus.map(i => i.value)
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    </script>
@endpush
