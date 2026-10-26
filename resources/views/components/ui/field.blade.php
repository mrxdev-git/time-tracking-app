@props(['name', 'label', 'invalid' => false])
@php($id = $attributes->get('id', $name))
<div class="field">
    <label for="{{ $id }}">{{ $label }}</label>
    <input {{ $attributes->merge(['id' => $id, 'name' => $name, 'type' => 'text', 'aria-invalid' => $invalid ? 'true' : false]) }}>
</div>
