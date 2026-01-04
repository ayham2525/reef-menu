@extends('adminlte::page')

@section('title', __('Create Order'))

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="mb-0">
            <i class="fa fa-plus-circle text-primary"></i> {{ __('Create Order') }}
        </h1>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
            <i class="fa fa-arrow-left mr-1"></i> {{ __('Back to Orders') }}
        </a>
    </div>
@stop

@section('content')
    <form action="{{ route('admin.orders.store') }}" method="POST" id="orderForm">
        @csrf

        {{-- Agency Name --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0">
                    <i class="fa fa-building mr-1"></i> {{ __('Agency') }}
                </h5>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label for="agency_name" class="mb-1">
                        {{ __('Agency Name') }} <span class="text-danger">*</span>
                    </label>
                    <input
                        type="text"
                        name="agency_name"
                        id="agency_name"
                        class="form-control @error('agency_name') is-invalid @enderror"
                        value="{{ old('agency_name') }}"
                        placeholder="{{ __('Enter agency name') }}"
                        required
                    >
                    @error('agency_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">{{ __('This is required to submit the order.') }}</small>
                </div>
            </div>
        </div>

        {{-- Notes --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa fa-info-circle mr-1"></i> {{ __('Order Notes') }}</h5>
            </div>
            <div class="card-body">
                <textarea name="notes" rows="2" class="form-control" placeholder="{{ __('Enter notes or special requests...') }}">{{ old('notes') }}</textarea>
            </div>
        </div>

        {{-- Category & Item Selection --}}
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fa fa-boxes mr-1"></i> {{ __('Add Items') }}</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>{{ __('Category') }}</label>
                        <select id="categorySelect" class="form-control" required>
                            <option value="">{{ __('Select Category') }}</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('Item') }}</label>
                        <select id="itemSelect" class="form-control" disabled required>
                            <option value="">{{ __('Select Item') }}</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>{{ __('Options') }}</label>
                        <select id="optionSelect" class="form-control" disabled>
                            <option value="">{{ __('No Options') }}</option>
                        </select>
                    </div>
                </div>

                <div class="text-right">
                    <button type="button" class="btn btn-success" id="addItem">
                        <i class="fa fa-plus mr-1"></i> {{ __('Add to Order') }}
                    </button>
                </div>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="card mt-4 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa fa-list mr-1"></i> {{ __('Order Items') }}</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0" id="itemsTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('Item Name') }}</th>
                                <th>{{ __('Option') }}</th>
                                <th width="120">{{ __('Qty') }}</th>
                                <th width="150">{{ __('Unit Price') }}</th>
                                <th width="150">{{ __('Line Total') }}</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Dynamic Rows --}}
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-right">
                <h5 class="mb-0">{{ __('Total:') }} <span id="totalAmount">0.00</span></h5>
            </div>
        </div>

        <div class="mt-4 text-right">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="fa fa-check mr-1"></i> {{ __('Submit Order') }}
            </button>
        </div>
    </form>
@stop

@section('js')
<script>
document.addEventListener('DOMContentLoaded', () => {
    let index = 0;
    const tableBody = document.querySelector('#itemsTable tbody');
    const totalDisplay = document.getElementById('totalAmount');

    const categorySelect = document.getElementById('categorySelect');
    const itemSelect     = document.getElementById('itemSelect');
    const optionSelect   = document.getElementById('optionSelect');
    const addItemBtn     = document.getElementById('addItem');

    const form          = document.getElementById('orderForm');
    const agencyInput   = document.getElementById('agency_name');

    // Load items by category (AJAX)
    categorySelect.addEventListener('change', async function() {
        const catId = this.value;
        itemSelect.innerHTML = `<option value="">{{ __('Loading...') }}</option>`;
        optionSelect.innerHTML = `<option value="">{{ __('No Options') }}</option>`;
        itemSelect.disabled = true;
        optionSelect.disabled = true;
        if (!catId) return;

        try {
            const res = await fetch(`/admin/orders/items-by-category/${catId}`);
            const data = await res.json();

            itemSelect.innerHTML = `<option value="">{{ __('Select Item') }}</option>`;
            data.items.forEach(item => {
                itemSelect.insertAdjacentHTML(
                    'beforeend',
                    `<option value="${item.id}" data-price="${item.price}">${item.name}</option>`
                );
            });

            itemSelect.disabled = false;
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Failed to load items.") }}' });
        }
    });

    // Load options by item (AJAX)
    itemSelect.addEventListener('change', async function() {
        const itemId = this.value;
        optionSelect.innerHTML = `<option value="">{{ __('Loading...') }}</option>`;
        optionSelect.disabled = true;
        if (!itemId) return;

        try {
            const res = await fetch(`/admin/orders/item-options/${itemId}`);
            const data = await res.json();

            optionSelect.innerHTML = `<option value="">{{ __('No Options') }}</option>`;
            if (data.options && data.options.length > 0) {
                optionSelect.innerHTML = `<option value="">{{ __('Select Option') }}</option>`;
                data.options.forEach(opt => {
                    optionSelect.insertAdjacentHTML(
                        'beforeend',
                        `<option value="${opt.id}" data-extra="${opt.extra_price}">${opt.name}</option>`
                    );
                });
                optionSelect.disabled = false;
            }
        } catch (err) {
            console.error(err);
            Swal.fire({ icon: 'error', title: '{{ __("Error") }}', text: '{{ __("Failed to load options.") }}' });
        }
    });

    // Add to table
    addItemBtn.addEventListener('click', () => {
        const itemId = itemSelect.value;
        const itemName = itemSelect.options[itemSelect.selectedIndex]?.text || '';
        const basePrice = parseFloat(itemSelect.options[itemSelect.selectedIndex]?.dataset.price || 0);

        if (!itemId) {
            Swal.fire({ icon: 'warning', title: '{{ __("Missing Item") }}', text: '{{ __("Please select an item.") }}' });
            return;
        }

        const optionId = optionSelect.value;
        const optionName = optionSelect.options[optionSelect.selectedIndex]?.text || '';
        const extraPrice = parseFloat(optionSelect.options[optionSelect.selectedIndex]?.dataset.extra || 0);

        const unitPrice = (basePrice + extraPrice).toFixed(2);

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <input type="hidden" name="items[${index}][item_id]" value="${itemId}">
                <input type="hidden" name="items[${index}][item_name]" value="${itemName}">
                ${itemName}
            </td>
            <td>
                <input type="hidden" name="items[${index}][option_id]" value="${optionId}">
                ${optionName && optionName !== '{{ __("Select Option") }}' ? optionName : '-'}
            </td>
            <td>
                <input type="number" name="items[${index}][quantity]" class="form-control qty" value="1" min="1">
            </td>
            <td>
                <input type="number" name="items[${index}][unit_price]" class="form-control unit" value="${unitPrice}" step="0.01" min="0">
            </td>
            <td>
                <input type="text" class="form-control line-total" value="${unitPrice}" readonly>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm remove-row">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        `;
        tableBody.appendChild(row);
        index++;
        recalcTotals();
    });

    // Input change handlers
    tableBody.addEventListener('input', (e) => {
        if (e.target.classList.contains('qty') || e.target.classList.contains('unit')) {
            const row = e.target.closest('tr');
            recalcRow(row);
            recalcTotals();
        }
    });

    // Remove row
    tableBody.addEventListener('click', (e) => {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            recalcTotals();
        }
    });

    function recalcRow(row) {
        const qty = parseFloat(row.querySelector('.qty')?.value || 0);
        const unit = parseFloat(row.querySelector('.unit')?.value || 0);
        const total = (qty * unit).toFixed(2);
        row.querySelector('.line-total').value = total;
    }

    function recalcTotals() {
        let total = 0;
        document.querySelectorAll('.line-total').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        totalDisplay.textContent = total.toFixed(2);
    }

    // ✅ Block submit if agency missing or no items
    form.addEventListener('submit', (e) => {
        const agency = (agencyInput.value || '').trim();
        const hasItems = tableBody.querySelectorAll('tr').length > 0;

        if (!agency) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: '{{ __("Missing Agency") }}', text: '{{ __("Agency name is required.") }}' });
            agencyInput.focus();
            return;
        }

        if (!hasItems) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: '{{ __("No Items") }}', text: '{{ __("Please add at least one item.") }}' });
            return;
        }
    });
});
</script>
@stop
