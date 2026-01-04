@component('mail::message')
# Low Stock Alert

**Item:** {{ $stock->item->name }}
**Warehouse:** {{ $stock->warehouse->name ?? 'Default' }}
**Current Quantity:** {{ $stock->formatQuantity() }}
**Minimum Required:** {{ $stock->min_quantity }} {{ $stock->unit_type }}

@component('mail::button', ['url' => url('/admin/inventory/' . $stock->id)])
View Stock
@endcomponent

Thanks,
Reef Menu System
@endcomponent
