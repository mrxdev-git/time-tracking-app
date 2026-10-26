<form method="POST" action="{{ route('employees.store') }}" class="panel form-row">
    @csrf
    <x-ui.field name="name" label="Employee name" :value="is_string(old('name')) ? old('name') : null" :invalid="$errors->has('name')" maxlength="100" required autocomplete="off" />
    <x-ui.button>Add employee</x-ui.button>
</form>
