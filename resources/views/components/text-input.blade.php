@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border-gray-300 focus:border-sig-blue focus:ring-sig-blue rounded-md shadow-sm']) }}>
