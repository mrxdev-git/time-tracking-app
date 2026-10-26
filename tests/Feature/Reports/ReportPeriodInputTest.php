<?php

declare(strict_types=1);

use App\Domain\Employees\Models\Employee;

it('rejects an invalid period with a visible message and no report', function (array $query, string $message): void {
    Employee::factory()->create(['name' => 'Alex Demo']);

    $this->get(route('report.show', $query))
        ->assertOk()
        ->assertSeeInOrder(['role="alert"', $message], false)
        ->assertSeeText('Show report')
        ->assertDontSeeText('Alex Demo')
        ->assertDontSee('<table', false);
})->with([
    'to before from' => [['from' => '2026-10-21', 'to' => '2026-10-20'], 'The to field must be a date after or equal to from.'],
    'malformed from' => [['from' => '20/10/2026', 'to' => '2026-10-21'], 'The from field must match the format Y-m-d.'],
    'impossible to date' => [['from' => '2026-10-20', 'to' => '2026-02-30'], 'The to field must match the format Y-m-d.'],
    'from before 1970' => [['from' => '1969-12-31', 'to' => '2026-10-21'], 'The from field must be a date after or equal to 1970-01-01.'],
    'only from' => [['from' => '2026-10-20'], 'The to field is required when from is present.'],
    'only to' => [['to' => '2026-10-21'], 'The from field is required when to is present.'],
    'to on 9999-12-31' => [['from' => '2026-10-20', 'to' => '9999-12-31'], 'The to field must be a date before 9999-12-31.'],
    'to beyond year 9999' => [['from' => '2026-10-20', 'to' => '10000-01-01'], 'The to field must match the format Y-m-d.'],
]);

it('defaults to the seven days ending today in UTC when no dates are given', function (array $query): void {
    freezeClock('2026-10-20 09:00:00');

    $this->get(route('report.show', $query))
        ->assertOk()
        ->assertSeeText('From 2026-10-14 to 2026-10-20 (inclusive)')
        ->assertDontSee('role="alert"', false);
})->with([
    'without parameters' => [[]],
    'with empty parameters' => [['from' => '', 'to' => '']],
]);
