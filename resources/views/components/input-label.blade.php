@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-label-md font-label-md mb-xs text-on-surface-variant']) }}>
    {{ $value ?? $slot }}
</label>
