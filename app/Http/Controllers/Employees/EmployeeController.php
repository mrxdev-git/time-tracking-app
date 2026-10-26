<?php

declare(strict_types=1);

namespace App\Http\Controllers\Employees;

use App\Domain\Employees\Actions\CreateEmployee;
use App\Domain\Employees\Models\Employee;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employees\StoreEmployeeRequest;
use App\Support\Clock\Clock;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

final class EmployeeController extends Controller
{
    public function index(Clock $clock): View
    {
        return view('employees.index', [
            'employees' => Employee::query()->with('runningEntry')->orderBy('name')->get(),
            'now' => $clock->now(),
        ]);
    }

    public function store(StoreEmployeeRequest $request, CreateEmployee $createEmployee): RedirectResponse
    {
        $employee = $createEmployee->execute($request->employeeName());

        return to_route('employees.index')->with('status', "Added $employee->name.");
    }
}
