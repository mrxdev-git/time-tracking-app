@props(['variant' => 'primary'])
<button {{ $attributes->class(['button', "button-$variant"])->merge(['type' => 'submit']) }}>{{ $slot }}</button>
