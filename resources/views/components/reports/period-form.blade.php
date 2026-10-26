@props(['from', 'to', 'fromInvalid' => false, 'toInvalid' => false])
<form method="GET" action="{{ route('report.show') }}" class="panel form-row">
    <x-ui.field name="from" type="date" label="From (UTC)" :value="$from" :invalid="$fromInvalid" min="1970-01-01" max="9999-12-30" required />
    <x-ui.field name="to" type="date" label="To (UTC)" :value="$to" :invalid="$toInvalid" min="1970-01-01" max="9999-12-30" required />
    <x-ui.button>Show report</x-ui.button>
</form>
