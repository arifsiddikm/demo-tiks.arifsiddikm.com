<div class="bg-white rounded-2xl border border-stone-200 overflow-hidden mb-4">
    {{-- Cinema Header --}}
    <div class="flex items-center gap-3 p-4 bg-stone-50 border-b border-stone-100">
        <div class="w-9 h-9 bg-brown-700 rounded-xl flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z"/></svg>
        </div>
        <div>
            <h3 class="font-semibold text-stone-800">{{ $cinema->name }}</h3>
            <p class="text-xs text-stone-400">{{ $cinema->city->name }} · {{ $cinema->address }}</p>
        </div>
    </div>
    {{-- Times --}}
    <div class="p-4">
        <div class="flex flex-wrap gap-2">
            @foreach($cinemaSchedules as $schedule)
            <button onclick="openSeatModal({{ $schedule->id }})"
                class="group flex flex-col items-center px-4 py-3 border-2 rounded-xl transition-all
                       {{ $schedule->available_seats > 20 ? 'border-stone-200 hover:border-brown-500 hover:bg-brown-50' : ($schedule->available_seats > 0 ? 'border-amber-300 hover:border-amber-500 hover:bg-amber-50' : 'border-red-200 bg-red-50 cursor-not-allowed opacity-60') }}"
                {{ $schedule->available_seats === 0 ? 'disabled' : '' }}>
                <span class="text-base font-bold text-stone-800 group-hover:text-brown-700">{{ $schedule->formatted_time }}</span>
                <span class="text-xs text-stone-500 mt-0.5">{{ $schedule->film_type }}</span>
                <span class="text-xs font-semibold mt-1 {{ $schedule->available_seats > 20 ? 'text-green-600' : ($schedule->available_seats > 0 ? 'text-amber-600' : 'text-red-500') }}">
                    {{ $schedule->available_seats === 0 ? 'Habis' : $schedule->available_seats . ' kursi' }}
                </span>
                <span class="text-xs text-brown-600 font-semibold mt-0.5">{{ $schedule->formatted_price }}</span>
            </button>
            @endforeach
        </div>
    </div>
</div>
