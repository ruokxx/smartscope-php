<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-4 py-2 bg-transparent border border-gray-500 rounded-md font-semibold text-xs text-gray-300 uppercase tracking-widest shadow-sm hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150']) }} style="border-color:rgba(255,255,255,0.2); color:rgba(255,255,255,0.7);">
    {{ $slot }}
</button>
