<button {{ $attributes->merge(['type' => 'submit', 'class' => 
'inline-flex items-center px-4 py-2 border border-transparent rounded-md 
font-semibold text-xs text-white uppercase tracking-widest text-[#00B570] hover:bg-[#13BEB4] transition-colors duration-200
focus:bg-[#0FA69D] active: bg-[#0FA69D] focus:outline-none focus:ring-2 focus:ring-indigo-500 
focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


{{-- class="bg-[#0FA69D] hover:bg-[#13BEB4] transition-colors duration-200" --}}