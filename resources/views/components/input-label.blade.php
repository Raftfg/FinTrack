@props(['value', 'required' => false])

<label {{ $attributes->merge(['class' => 'block text-label-md font-label-md mb-xs text-on-surface-variant']) }}>
    {{ $value ?? $slot }}@if($required) <span class="text-red-500 font-bold">*</span>@endif
</label>
