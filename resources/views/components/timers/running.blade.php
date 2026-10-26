@props(['employee', 'entry', 'now'])
<div class="timer">
    <p class="timer-task"><span class="badge">running</span> {{ $entry->description }}</p>
    <p class="timer-meta">Started <x-ui.time :at="$entry->started_at" /> UTC</p>
    <div class="timer-controls">
        <p class="timer-elapsed"><span class="timer-label">Elapsed</span> <x-timers.elapsed :entry="$entry" :now="$now" /></p>
        <form method="POST" action="{{ route('timer.stop', $employee) }}">
            @csrf
            <x-ui.button variant="danger">Stop<span class="visually-hidden"> timer for {{ $employee->name }}</span></x-ui.button>
        </form>
    </div>
</div>
