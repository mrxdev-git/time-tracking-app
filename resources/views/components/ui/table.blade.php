@props(['label'])
<div class="table-wrap" role="region" aria-label="{{ $label }}" tabindex="0">
    <table>
        {{ $slot }}
    </table>
</div>
