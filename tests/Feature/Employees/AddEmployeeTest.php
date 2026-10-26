<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;

beforeEach(function (): void {
    $this->from(route('employees.index'));
});

it('adds an employee, confirms it and lists it on the index', function (string $submitted, string $stored): void {
    $this->post(route('employees.store'), ['name' => $submitted])
        ->assertRedirect(route('employees.index'))
        ->assertSessionHas('status', "Added $stored.");

    expect(Employee::query()->pluck('name')->all())->toBe([$stored]);

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSeeInOrder(["Added $stored.", $stored])
        ->assertDontSee('No employees yet.');
})->with([
    'surrounding whitespace trimmed' => ['  Alex Demo  ', 'Alex Demo'],
    'exactly 100 characters' => [str_repeat('a', 100), str_repeat('a', 100)],
]);

it('rejects an invalid name with a visible message and adds no employee', function (string $name, string $message): void {
    $this->post(route('employees.store'), ['name' => $name])
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseEmpty('employees');

    $this->get(route('employees.index'))->assertSee($message);
})->with([
    'empty' => ['', 'The name field is required.'],
    'whitespace only' => ['   ', 'The name field is required.'],
    'longer than 100 characters' => [str_repeat('a', 101), 'The name field must not be greater than 100 characters.'],
]);

it('rejects a duplicate name regardless of case and surrounding whitespace', function (string $name, string $message): void {
    Employee::factory()->create(['name' => 'Alex Demo']);

    $this->post(route('employees.store'), ['name' => $name])
        ->assertRedirect(route('employees.index'));

    expect(Employee::query()->pluck('name')->all())->toBe(['Alex Demo']);

    $this->get(route('employees.index'))->assertSee($message);
})->with([
    'identical' => ['Alex Demo', 'An employee named "Alex Demo" already exists.'],
    'different case' => ['ALEX DEMO', 'An employee named "ALEX DEMO" already exists.'],
    'different case and surrounding whitespace' => ['  alex demo  ', 'An employee named "alex demo" already exists.'],
]);
