<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('time_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('employee_id')->constrained();
            $table->string('description', 255);
            $table->dateTime('started_at');
            $table->dateTime('stopped_at')->nullable();
            $table->timestamps();
            $table->index(['employee_id', 'started_at']);
        });

        DB::statement('create unique index time_entries_one_running_per_employee on time_entries (employee_id) where stopped_at is null');
    }

    public function down(): void
    {
        Schema::dropIfExists('time_entries');
    }
};
