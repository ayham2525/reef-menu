<?php
// app/Http/Controllers/Admin/EmployeeController.php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Employee\StoreEmployeeRequest;
use App\Http\Requests\Employee\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $base = Employee::query()
            ->with(['user', 'position', 'section'])
            ->when($request->filled('search'), function ($query) use ($request) {
                $s = $request->string('search')->toString();
                $query->where(function ($qq) use ($s) {
                    $qq->where('employee_code', 'like', "%{$s}%")
                        ->orWhere('phone', 'like', "%{$s}%")
                        ->orWhereHas('user', fn($u) => $u->where('name', 'like', "%{$s}%")
                            ->orWhere('email', 'like', "%{$s}%"));
                });
            });

        $totalActive   = (clone $base)->where('is_active', true)->count();
        $totalInactive = (clone $base)->where('is_active', false)->count();

        $status = $request->get('status', 'all'); // all|active|inactive
        $list = (clone $base)
            ->when($status === 'active', fn($q) => $q->where('is_active', true))
            ->when($status === 'inactive', fn($q) => $q->where('is_active', false))
            ->orderByRaw('is_active DESC')
            ->orderBy('employee_code');

        $employees = $list->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return view('admin.employees.partials.table', compact('employees'))->render();
        }

        return view('admin.employees.index', compact('employees', 'status', 'totalActive', 'totalInactive'));
    }


    public function create()
    {
        $positions = Position::orderBy('sort_order')->orderBy('name')->get();
        $sections  = Section::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.employees.create', compact('positions', 'sections'));
    }

    public function store(StoreEmployeeRequest $request)
    {
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name'     => $request->string('user_name'),
                'email'    => $request->string('user_email'),
                // cast 'password' => 'hashed' in User model will hash it
                'password' => $request->string('user_password'),
            ]);

            $data = $request->validated();
            $payload = [
                'user_id'       => $user->id,
                'position_id'   => $data['position_id'],
                'section_id'    => $data['section_id'] ?? null,
                'employee_code' => $data['employee_code'] ?? null,
                'phone'         => $data['phone'] ?? null,
                'national_id'   => $data['national_id'] ?? null,
                'gender'        => $data['gender'] ?? null,
                'birth_date'    => $data['birth_date'] ?? null,
                'hired_at'      => $data['hired_at'] ?? null,
                'terminated_at' => $data['terminated_at'] ?? null,
                'is_active'     => $request->boolean('is_active'),
                'notes'         => $data['notes'] ?? null,
                'created_by'    => Auth::id(),
                'updated_by'    => Auth::id(),
            ];

            Employee::create($payload);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee created successfully.');
    }

    public function edit(Employee $employee)
    {
        $employee->load(['user', 'position', 'section']);
        $positions = Position::orderBy('sort_order')->orderBy('name')->get();
        $sections  = Section::orderBy('sort_order')->orderBy('name')->get();

        return view('admin.employees.edit', compact('employee', 'positions', 'sections'));
    }

    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        DB::transaction(function () use ($request, $employee) {
            // Update linked user
            $employee->user->update([
                'name'  => $request->string('user_name'),
                'email' => $request->string('user_email'),
                // Update password only if provided
                'password' => $request->filled('user_password') ? $request->string('user_password') : $employee->user->password,
            ]);

            $data = $request->validated();
            $payload = [
                'position_id'   => $data['position_id'],
                'section_id'    => $data['section_id'] ?? null,
                'employee_code' => $data['employee_code'] ?? $employee->employee_code,
                'phone'         => $data['phone'] ?? null,
                'national_id'   => $data['national_id'] ?? null,
                'gender'        => $data['gender'] ?? null,
                'birth_date'    => $data['birth_date'] ?? null,
                'hired_at'      => $data['hired_at'] ?? null,
                'terminated_at' => $data['terminated_at'] ?? null,
                'is_active'     => $request->boolean('is_active'),
                'notes'         => $data['notes'] ?? null,
                'updated_by'    => Auth::id(),
            ];

            $employee->update($payload);
        });

        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    public function show(Employee $employee)
    {
        $employee->load(['user', 'position', 'section']);
        return view('admin.employees.show', compact('employee'));
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();
        return redirect()->route('admin.employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
}
