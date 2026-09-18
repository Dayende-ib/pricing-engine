<div class="space-y-6">
    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 dark:border-emerald-500/20 dark:bg-emerald-500/10">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 dark:bg-emerald-500/20">
                <flux:icon icon="check" class="size-4 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div class="text-sm font-medium text-emerald-800 dark:text-emerald-300">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl border border-red-200 bg-red-50 px-5 py-4 dark:border-red-500/20 dark:bg-red-500/10">
            <div class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-red-100 dark:bg-red-500/20">
                <flux:icon icon="x-circle" class="size-4 text-red-600 dark:text-red-400" />
            </div>
            <div class="text-sm font-medium text-red-800 dark:text-red-300">{{ session('error') }}</div>
        </div>
    @endif

    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl" class="truncate">{{ $project->name }}</flux:heading>
                <x-status-badge :status="$project->status->value">
                    {{ $project->status->label() }}
                </x-status-badge>
            </div>
            <flux:text size="sm" class="mt-1.5 text-zinc-500 dark:text-zinc-400">
                @if($project->client_name)
                    {{ $project->client_name }}@if($project->client_email) · {{ $project->client_email }}@endif
                @else
                    Client non défini
                @endif
                · Créé le {{ $project->created_at->format('d/m/Y') }}
            </flux:text>
        </div>

        <div class="flex flex-wrap gap-2">
            @if(in_array($project->status->value, ['draft', 'analyzing', 'review'], true))
                <flux:button variant="primary" icon="calculator" wire:click="calculatePricing" wire:loading.attr="disabled">
                    Calculer le prix
                </flux:button>
            @endif

            @if($project->status->value === 'priced')
                <flux:button variant="primary" icon-trailing="arrow-right" icon="document-text" wire:click="generateQuote" wire:loading.attr="disabled">
                    Générer le devis
                </flux:button>
            @endif

            <flux:button variant="outline" icon="pencil" href="{{ route('projects.edit', $project) }}" wire:navigate>
                Modifier
            </flux:button>
        </div>
    </div>

    {{-- Tabs Card --}}
    <div class="overflow-hidden rounded-2xl border border-zinc-200/60 bg-white dark:border-white/[0.06] dark:bg-surface">
        {{-- Tab Navigation --}}
        <div class="flex gap-1 overflow-x-auto border-b border-zinc-100 p-2 dark:border-white/[0.06]">
            @foreach([
                'overview' => ['label' => "Vue d'ensemble", 'icon' => 'eye'],
                'features' => ['label' => 'Fonctionnalités', 'icon' => 'squares-2x2'],
                'external-costs' => ['label' => 'Coûts externes', 'icon' => 'banknotes'],
                'adjustments' => ['label' => 'Ajustements', 'icon' => 'adjustments-horizontal'],
            ] as $tab => $item)
                <button
                    wire:click="setActiveTab('{{ $tab }}')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                        'bg-teal-50 text-teal-700 shadow-sm dark:bg-teal-500/10 dark:text-teal-400 dark:shadow-[0_0_12px_-3px_rgba(45,212,191,0.2)]' => $activeTab === $tab,
                        'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/[0.04] dark:hover:text-zinc-200' => $activeTab !== $tab,
                    ])
                >
                    <flux:icon :icon="$item['icon']" variant="micro" class="size-4" />
                    {{ $item['label'] }}
                    @php $count = $project->{$tab === 'overview' ? 'features' : match($tab) { 'external-costs' => 'externalCosts', 'adjustments' => 'pricingAdjustments', default => 'features' }}->count(); @endphp
                    @if($tab !== 'overview' && $count > 0)
                        <span @class([
                            'rounded-full px-2 py-0.5 text-xs font-semibold',
                            'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400' => $activeTab === $tab,
                            'bg-zinc-100 text-zinc-500 dark:bg-white/[0.06] dark:text-zinc-400' => $activeTab !== $tab,
                        ])>
                            {{ $count }}
                        </span>
                    @endif
                </button>
            @endforeach

            @if($project->quotes->isNotEmpty())
                <button
                    wire:click="setActiveTab('quotes')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                        'bg-teal-50 text-teal-700 shadow-sm dark:bg-teal-500/10 dark:text-teal-400 dark:shadow-[0_0_12px_-3px_rgba(45,212,191,0.2)]' => $activeTab === 'quotes',
                        'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/[0.04] dark:hover:text-zinc-200' => $activeTab !== 'quotes',
                    ])
                >
                    <flux:icon icon="document-text" variant="micro" class="size-4" />
                    Devis
                    <span @class([
                        'rounded-full px-2 py-0.5 text-xs font-semibold',
                        'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400' => $activeTab === 'quotes',
                        'bg-zinc-100 text-zinc-500 dark:bg-white/[0.06] dark:text-zinc-400' => $activeTab !== 'quotes',
                    ])>
                        {{ $project->quotes->count() }}
                    </span>
                </button>
            @endif

            @if($project->aiAnalyses->isNotEmpty())
                <button
                    wire:click="setActiveTab('ai')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-xl px-4 py-2.5 text-sm font-medium transition-all duration-200',
                        'bg-teal-50 text-teal-700 shadow-sm dark:bg-teal-500/10 dark:text-teal-400 dark:shadow-[0_0_12px_-3px_rgba(45,212,191,0.2)]' => $activeTab === 'ai',
                        'text-zinc-500 hover:bg-zinc-100 hover:text-zinc-700 dark:text-zinc-400 dark:hover:bg-white/[0.04] dark:hover:text-zinc-200' => $activeTab !== 'ai',
                    ])
                >
                    <flux:icon icon="sparkles" variant="micro" class="size-4" />
                    Analyse IA
                    <span @class([
                        'rounded-full px-2 py-0.5 text-xs font-semibold',
                        'bg-teal-100 text-teal-700 dark:bg-teal-500/20 dark:text-teal-400' => $activeTab === 'ai',
                        'bg-zinc-100 text-zinc-500 dark:bg-white/[0.06] dark:text-zinc-400' => $activeTab !== 'ai',
                    ])>
                        {{ $project->aiAnalyses->count() }}
                    </span>
                </button>
            @endif
        </div>

        {{-- Tab Content --}}
        <div class="p-6">
            @switch($activeTab)
                @case('overview')
                    @include('livewire.projects.tabs.overview')
                    @break
                @case('features')
                    @include('livewire.projects.tabs.features')
                    @break
                @case('external-costs')
                    @include('livewire.projects.tabs.external-costs')
                    @break
                @case('adjustments')
                    @include('livewire.projects.tabs.adjustments')
                    @break
                @case('quotes')
                    @include('livewire.projects.tabs.quotes')
                    @break
                @case('ai')
                    @include('livewire.projects.tabs.ai')
                    @break
            @endswitch
        </div>
    </div>

    {{-- Modals --}}
    <flux:modal wire:model="showFeatureModal">
        @include('livewire.projects.modals.feature')
    </flux:modal>

    <flux:modal wire:model="showExternalCostModal">
        @include('livewire.projects.modals.external-cost')
    </flux:modal>

    <flux:modal wire:model="showAdjustmentModal">
        @include('livewire.projects.modals.adjustment')
    </flux:modal>
</div>
