@props(['href'])

@php
$user = Auth::user();
$isDisabledProfile = $user && $user->role === 'petugas' && $href === route('profile.edit');
@endphp

<a {{ $attributes->merge([
        'class' => 'flex items-center gap-2 w-full px-4 py-2 text-start text-sm leading-5 transition duration-150 ease-in-out ' .
                    ($isDisabledProfile
                        ? 'text-gray-700 cursor-not-allowed'
                        : 'text-gray-700 dark:text-gray-300 hover:bg-green-100 dark:hover:bg-green-800 focus:outline-none focus:bg-green-100 dark:focus:bg-green-800')
    ]) }} href="{{ $isDisabledProfile ? '#' : $href }}" @if($isDisabledProfile) onclick="event.preventDefault();"
    @endif>
    {{-- slot pertama = ikon --}}
    @isset($icon)
    <span class="w-5 h-5 flex items-center justify-center">
        {{ $icon }}
    </span>
    @endisset

    {{-- slot kedua = teks --}}
    <span>{{ $slot }}</span>
</a>