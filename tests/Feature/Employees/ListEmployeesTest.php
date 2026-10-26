<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;

it('shows an empty state when there are no employees', function (): void {
    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSee('No employees yet.');
});

it('lists every employee ordered by name', function (): void {
    Employee::factory()->create(['name' => 'Taylor Example']);
    Employee::factory()->create(['name' => 'Alex Demo']);
    Employee::factory()->create(['name' => 'Jordan Sample']);

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSeeInOrder(['Alex Demo', 'Jordan Sample', 'Taylor Example'])
        ->assertDontSee('No employees yet.');
});
