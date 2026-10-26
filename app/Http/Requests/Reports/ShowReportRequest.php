<?php

declare(strict_types=1);

namespace App\Http\Requests\Reports;

use App\Domain\Reports\Period;
use App\Support\Clock\Clock;
use Illuminate\Contracts\Support\MessageBag;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

final class ShowReportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'from' => ['required_with:to', 'date_format:Y-m-d', 'after_or_equal:1970-01-01'],
            'to' => ['required_with:from', 'date_format:Y-m-d', 'after_or_equal:from', 'before:9999-12-31'],
        ];
    }

    public function period(): ?Period
    {
        if ($this->validationErrors()->isNotEmpty()) {
            return null;
        }

        return Period::fromDates($this->validated('from'), $this->validated('to'));
    }

    public function validationErrors(): MessageBag
    {
        return $this->validator->errors();
    }

    public function dateInput(string $key): string
    {
        $value = $this->input($key);

        return is_string($value) ? $value : '';
    }

    protected function prepareForValidation(): void
    {
        if ($this->anyFilled(['from', 'to'])) {
            return;
        }

        $today = $this->container->make(Clock::class)->now();

        $this->merge([
            'from' => $today->subDays(6)->toDateString(),
            'to' => $today->toDateString(),
        ]);
    }

    protected function failedValidation(Validator $validator): void {}
}
