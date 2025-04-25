@props(['label', 'model', 'options'])

<div
    x-data="{
        open: false,
        selected: '{{ ucfirst($model) }}',
        dropdownPosition: 'bottom',
        toggleDropdown() {
            this.open = !this.open;
            this.$nextTick(() => {
                const rect = $el.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;
                const spaceAbove = rect.top;

                this.dropdownPosition = spaceBelow < 200 && spaceAbove > 200 ? 'top' : 'bottom';
            });
        }
    }"
    class="relative"
>
    <button @click="toggleDropdown"
        class="w-full px-4 py-3 rounded-lg bg-white/20 text-white text-left text-lg border border-white/20 focus:ring-2 focus:ring-pink-400 focus:outline-none">
        <span
            x-text="Object.values({{ Js::from($options) }})[Object.keys({{ Js::from($options) }}).indexOf(selected)] || '{{ $label }}'">
        </span>
        <svg class="absolute right-4 top-3.5 h-5 w-5 text-white/80" fill="none" stroke="currentColor"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" @click.away="open = false" x-transition
        :class="dropdownPosition === 'top' ? 'bottom-full mb-2' : 'mt-2'"
        class="absolute z-50 w-full bg-gradient-to-br from-gray-900 via-indigo-900 to-gray-800 text-white rounded-lg shadow-xl backdrop-blur-md border border-white/20 overflow-hidden">
        <ul class="text-lg">
            @foreach($options as $value => $label)
                <li @click="selected = '{{ $value }}'; open = false; $wire.set('{{ $model }}', '{{ $value }}')"
                    class="px-4 py-3 hover:bg-white/20 cursor-pointer">{{ $label }}</li>
            @endforeach
        </ul>
    </div>
</div>
