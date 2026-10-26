<?php

declare(strict_types=1);

namespace App\Http\Requests\Timers;

use Illuminate\Foundation\Http\FormRequest;

final class StartTimerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:255'],
        ];
    }

    public function taskDescription(): string
    {
        return $this->validated('description');
    }
}
