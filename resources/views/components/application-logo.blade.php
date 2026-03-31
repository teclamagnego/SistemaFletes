@php
    $empresa = \App\Models\Empresa::first();
@endphp

@if($empresa && $empresa->logo)
    <img src="{{ asset('storage/' . $empresa->logo) }}" {{ $attributes->merge(['style' => 'max-height: 100px; width: auto;']) }}>
@else
    <span class="text-xl font-bold text-gray-800">DOBLE G</span>
@endif
