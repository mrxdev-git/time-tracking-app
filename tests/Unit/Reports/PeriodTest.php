<?php

declare(strict_types=1);

use App\Domain\Reports\Period;

it('runs from midnight UTC of from up to the exclusive midnight UTC after to', function (string $from, string $to, string $start, string $end): void {
    $period = Period::fromDates($from, $to);

    expect($period->start()->format('Y-m-d H:i:s e'))->toBe($start)
        ->and($period->end()->format('Y-m-d H:i:s e'))->toBe($end);
})->with([
    'two days' => ['2026-10-20', '2026-10-21', '2026-10-20 00:00:00 UTC', '2026-10-22 00:00:00 UTC'],
    'a single day' => ['2026-10-20', '2026-10-20', '2026-10-20 00:00:00 UTC', '2026-10-21 00:00:00 UTC'],
    'across the end of a year' => ['2026-12-31', '2027-01-01', '2026-12-31 00:00:00 UTC', '2027-01-02 00:00:00 UTC'],
    'up to a leap day' => ['2028-02-28', '2028-02-29', '2028-02-28 00:00:00 UTC', '2028-03-01 00:00:00 UTC'],
]);

it('keeps from and to as the inclusive calendar dates', function (): void {
    $period = Period::fromDates('2026-10-20', '2026-10-21');

    expect($period->from->format('Y-m-d H:i:s e'))->toBe('2026-10-20 00:00:00 UTC')
        ->and($period->to->format('Y-m-d H:i:s e'))->toBe('2026-10-21 00:00:00 UTC');
});
