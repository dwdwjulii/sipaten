@props([
'type' => 'info', // error | success | warning
'title' => '',
'messages' => [], // array of ['text' => '', 'status' => 'success'|'error']
])

@php
$styles = [
'error' => [
'bg' => 'bg-red-100',
'text' => 'text-red-900',
'iconBg' => 'bg-red-500',
'icon' => asset('asset/images/error.png'),
'close' => 'text-red-600 hover:text-red-700',
],
'success' => [
'bg' => 'bg-emerald-100',
'text' => 'text-green-900',
'iconBg' => 'bg-green-500',
'icon' => asset('asset/images/success.png'),
'close' => 'text-green-600 hover:text-green-700',
],
'warning' => [
'bg' => 'bg-orange-100',
'text' => 'text-orange-800',
'iconBg' => 'bg-orange-500',
'icon' => asset('asset/images/warning.png'),
'close' => 'text-orange-600 hover:text-orange-700',
],
];
$style = $styles[$type];
@endphp

<div x-data="{ open: true }" x-show="open" x-transition
    class="{{ $style['bg'] }} rounded-sm flex overflow-hidden relative">

    {{-- Icon Besar dengan Background Dark --}}
    <div class="w-12 {{ $style['iconBg'] }} flex items-center justify-center">
        <img src="{{ $style['icon'] }}" alt="icon" class="w-6 h-6 object-contain">
    </div>

    {{-- Konten --}}
    <div class="flex-1 px-3 py-3 flex items-start">
        <div class="flex-1 flex flex-col justify-center">
            <p class="text-sm {{ $style['text'] }}">
                <span class="font-semibold">{{ $title }}</span>
                @if(is_array($messages))
                {{ implode(' ', array_map(fn($msg) => is_array($msg) ? $msg['text'] : $msg, $messages)) }}
                @else
                {{ $messages }}
                @endif
            </p>
        </div>
    </div>


    {{-- Close Button Top Right --}}
    <button @click="open = false" class="absolute top-2 right-2 {{ $style['close'] }} focus:outline-none">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
            <path fill-rule="evenodd"
                d="M5.47 5.47a.75.75 0 0 1 1.06 0L12 10.94l5.47-5.47a.75.75 0 1 1 1.06 1.06L13.06 12l5.47 5.47a.75.75 0 1 1-1.06 1.06L12 13.06l-5.47 5.47a.75.75 0 0 1-1.06-1.06L10.94 12 5.47 6.53a.75.75 0 0 1 0-1.06Z"
                clip-rule="evenodd" />
        </svg>
    </button>
</div>