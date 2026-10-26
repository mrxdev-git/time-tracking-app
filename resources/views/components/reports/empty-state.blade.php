@props(['period'])
<p class="empty-state">No time entries between {{ $period->from->toDateString() }} and {{ $period->to->toDateString() }}.</p>
