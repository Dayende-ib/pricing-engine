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
        'priced' => 'teal',
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
        'yearly' => 'emerald',
        'percentage' => 'violet',
        'fixed' => 'orange',
        'low' => 'emerald',
        'normal' => 'blue',
        'high' => 'amber',
        'very_high' => 'red',
        'critical' => 'red',
        'ai' => 'violet',
        'human' => 'zinc',
        'template' => 'blue',
        'historical' => 'emerald',
        'web_app' => 'blue',
        'mobile_app' => 'indigo',
        'api' => 'violet',
        'ecommerce' => 'amber',
        'dashboard' => 'teal',
        'landing_page' => 'emerald',
        'other' => 'zinc',
    ];

    $color = $color ?? ($map[$status] ?? 'zinc');
@endphp

<flux:badge :color="$color" :icon="$icon" rounded>
    {{ $slot }}
</flux:badge>
