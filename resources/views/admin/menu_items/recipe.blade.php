@extends('adminlte::page')

@section('content')

<h3 class="mb-3">Recipe / Ingredients — {{ $item->name }}</h3>

<div class="card mb-4">
    <div class="card-header">Add Ingredient</div>
    <div class="card-body">

        <form method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Ingredient Name</label>
                    <input type="text" name="ingredient_name" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Quantity</label>
                    <input type="number" step="0.001" name="quantity" class="form-control" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Unit</label>
                    <select name="unit_type" class="form-select" required>
                        @foreach($units as $u)
                            <option value="{{ $u }}">{{ strtoupper($u) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-primary w-100">Add</button>
                </div>
            </div>
        </form>

    </div>
</div>

@if($ingredients->count())
<div class="card">
    <div class="card-header">Ingredients List</div>
    <div class="table-responsive">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th width="60"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($ingredients as $r)
                <tr>
                    <td>{{ $r->ingredient_name }}</td>
                    <td>{{ $r->quantity }}</td>
                    <td>{{ $r->unit_type }}</td>
                    <td>
                        <form method="POST" action="{{ route('admin.menu-items.recipeDelete', [$item, $r]) }}">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection
