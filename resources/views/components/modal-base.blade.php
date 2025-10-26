{{-- File: resources/views/components/modal-base.blade.php --}}

@props([
'title' => 'Modal Title',
'show' => false,
'maxWidth' => 'md', // default ukuran
'scrollable' => true, // default: modal bisa scroll
'showTitle' => true,
])

@php
$maxWidthClass = [
'sm' => 'sm:max-w-sm',
'md' => 'sm:max-w-md',
'lg' => 'sm:max-w-lg',
'xl' => 'sm:max-w-xl',
'2xl' => 'sm:max-w-2xl',
][$maxWidth] ?? 'sm:max-w-md';

// bodyClass otomatis menyesuaikan
$bodyClass = $scrollable
? 'flex-1 min-h-[56vh] max-h-[56vh] overflow-y-auto scrollbar-hide pr-1'
: 'flex-1';
@endphp

<div x-data="{ 
        open: @js($show), 
        lockBodyScroll() { document.body.style.overflow = 'hidden' }, 
        unlockBodyScroll() { document.body.style.overflow = '' } 
    }" x-init="$watch('open', val => val ? lockBodyScroll() : unlockBodyScroll())" x-cloak>

    {{-- Trigger --}}
    @if (isset($trigger))
    <div @click="open = true">
        {{ $trigger }}
    </div>
    @endif

    {{-- Overlay --}}
    <div x-show="open" x-transition.opacity.duration.200ms
        class="fixed inset-0 z-50 flex items-start justify-center bg-black bg-opacity-40">

        {{-- Modal box --}}
        <div @click.outside="
                if (!$event.target.closest('.datepicker-dropdown')) {
                    open = false
                }
            "
            class="bg-white rounded-xl shadow-2xl w-full {{ $maxWidthClass }} p-5 mt-10 transform transition-transform"
            x-show="open" x-transition:enter="transform -translate-y-10 opacity-0"
            x-transition:enter-end="transform translate-y-0 opacity-100"
            x-transition:leave="transform translate-y-0 opacity-100"
            x-transition:leave-end="transform -translate-y-10 opacity-0" x-transition.duration.300ms.ease-out>

            {{-- Header --}}
            @if ($showTitle && $title)
            <div class="mb-1">
                <h2 class="text-lg font-semibold text-gray-900">{{ $title }}</h2>
                {{ $header ?? '' }}
            </div>
            @endif

            {{-- Body --}}
            <div class="space-y-4 relative">
                <div class="{{ $bodyClass }}">
                    {{ $slot }}
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-2 pt-4">
                {{ $footer ?? '' }}
            </div>
        </div>
    </div>
</div>