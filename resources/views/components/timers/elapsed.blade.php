@props(['entry', 'now'])
<span class="elapsed" role="timer" data-started-at="{{ $entry->started_at->toIso8601ZuluString() }}" data-server-now="{{ $now->toIso8601ZuluString() }}" x-data="elapsed" x-text="display">{{ $entry->duration($now)->format() }}</span>
