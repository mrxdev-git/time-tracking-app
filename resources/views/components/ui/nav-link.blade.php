@props(['route'])
<a {{ $attributes->merge(['href' => route($route), 'aria-current' => request()->routeIs($route) ? 'page' : false]) }}>{{ $slot }}</a>
