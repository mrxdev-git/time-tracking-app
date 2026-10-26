@props(['at'])
<time datetime="{{ $at->toIso8601ZuluString() }}">{{ $at->utc()->format('Y-m-d H:i:s') }}</time>
