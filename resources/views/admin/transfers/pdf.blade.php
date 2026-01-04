<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Transfer #{{ $transfer->id }}</title>

    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #000; padding: 6px; }
        th { background: #f0f0f0; }
        h2 { margin-bottom: 0; }
    </style>
</head>

<body>

<h2>Warehouse Transfer #{{ $transfer->id }}</h2>

<p>
    <strong>From:</strong> {{ $transfer->fromWarehouse->name }} <br>
    <strong>To:</strong> {{ $transfer->toWarehouse->name }} <br>
    <strong>Status:</strong> {{ ucfirst($transfer->status) }} <br>
</p>

<h4>Items</h4>

<table>
    <thead>
        <tr>
            <th>Item</th>
            <th width="20%">Qty</th>
            <th width="20%">Unit</th>
        </tr>
    </thead>

    <tbody>
        @foreach($transfer->items as $row)
        <tr>
            <td>{{ $row->item->name }}</td>
            <td>{{ $row->quantity }}</td>
            <td>{{ $row->unit_type }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
