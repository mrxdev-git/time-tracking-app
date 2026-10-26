<?php

declare(strict_types=1);

it('rejects a name sent as a list and renders the employees page again', function (): void {
    $this->from(route('employees.index'))
        ->post(route('employees.store'), ['name' => ['Alex Demo']])
        ->assertRedirect(route('employees.index'));

    $this->assertDatabaseEmpty('employees');

    $this->get(route('employees.index'))
        ->assertOk()
        ->assertSee('The name field must be a string.');
});
