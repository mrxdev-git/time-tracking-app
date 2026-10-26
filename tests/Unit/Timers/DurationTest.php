<?php

declare(strict_types=1);

use App\Domain\Timers\Duration;
use Carbon\CarbonImmutable;

it('formats seconds as unbounded hours with zero-padded minutes and seconds', function (int $seconds, string $formatted): void {
    expect(Duration::fromSeconds($seconds)->format())->toBe($formatted);
})->with([
    'zero' => [0, '0:00:00'],
    'under a minute' => [59, '0:00:59'],
    'just under an hour' => [3599, '0:59:59'],
    'one hour' => [3600, '1:00:00'],
    'more than a day' => [100000, '27:46:40'],
]);

it('adds two durations', function (): void {
    expect(Duration::fromSeconds(3600)->add(Duration::fromSeconds(59))->seconds)->toBe(3659);
});

it('measures the whole seconds between two instants', function (): void {
    $start = CarbonImmutable::parse('2026-10-20 09:00:00', 'UTC');

    expect(Duration::between($start, $start->addHour()->addMinutes(30)->addSeconds(15))->seconds)->toBe(5415);
});
