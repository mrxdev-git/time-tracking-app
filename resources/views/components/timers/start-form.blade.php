@props(['employee'])
<form method="POST" action="{{ route('timer.start', $employee) }}" class="form-row">
    @csrf
    <x-ui.field name="description" id="description-{{ $employee->id }}" maxlength="255" required autocomplete="off">
        <x-slot:label>Task description<span class="visually-hidden"> for {{ $employee->name }}</span></x-slot:label>
    </x-ui.field>
    <x-ui.button>Start<span class="visually-hidden"> timer for {{ $employee->name }}</span></x-ui.button>
</form>
