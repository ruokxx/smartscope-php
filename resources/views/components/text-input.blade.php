@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'border-gray-700 bg-gray-900 bg-opacity-50 text-white focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm']) !!} style="background: rgba(0, 0, 0, 0.2); border: 1px solid rgba(255, 255, 255, 0.1); color: #e6eef6;">
