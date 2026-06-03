<button {{ $attributes->merge(['type' => 'submit', 'class' => 'w-full flex items-center justify-center px-6 py-3 bg-primary text-on-primary hover:bg-primary-container transition-all duration-200 rounded-xl font-label-md text-label-md font-bold shadow-md active:scale-95 cursor-pointer']) }}>
    {{ $slot }}
</button>
