@props([
    'status' => null,
    'color' => null,
    'icon' => null,
])

@php
    $map = [
        'draft' => 'zinc',
        'analyzing' => 'blue',
        'review' => 'amber',
        'priced' => 'purple',
        'quoted' => 'indigo',
        'accepted' => 'emerald',
        'rejected' => 'red',
        'in_progress' => 'blue',
        'completed' => 'teal',
        'cancelled' => 'zinc',
        'sent' => 'blue',
        'viewed' => 'indigo',
        'expired' => 'zinc',
        'pending' => 'zinc',
        'processing' => 'blue',
        'failed' => 'red',
        'one_time' => 'zinc',
        'monthly' => 'blue',
        'yearly' => 'green',
        'percentage' => 'purple',
        'fixed' => 'orange',
        'low' => 'green',
        'normal' => 'blue',
        'high' => 'amber',
        'very_high' => 'red',
        'critical' => 'red',
        'ai' => 'purple',
        'human' => 'zinc',
        'template' => 'blue',
        'historical' => 'green',
        'web_app' => 'blue',
        'mobile_app' => 'indigo',
        'api' => 'purple',
        'ecommerce' => 'amber',
        'dashboard' => 'teal',
        'landing_page' => 'green',
        'other' => 'zinc',
    ];

    $color = $color ?? ($map[$status] ?? 'zinc');
@endphp

<flux:badge :color="$color" :icon="$icon" rounded>
    {{ $slot }}
</flux:badge>