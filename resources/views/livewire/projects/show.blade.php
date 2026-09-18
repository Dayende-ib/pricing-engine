<div class="space-y-8">
    @if(session('success'))
        <flux:callout icon="check" color="emerald">
            <flux:callout.text>{{ session('success') }}</flux:callout.text>
        </flux:callout>
    @endif

    @if(session('error'))
        <flux:callout icon="x-circle" color="red">
            <flux:callout.text>{{ session('error') }}</flux:callout.text>
        </flux:callout>
    @endif

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-3">
                <flux:heading size="xl" class="truncate">{{ $project->name }}</flux:heading>
                <x-status-badge :status="$project->status->value">
                    {{ $project->status->label() }}
                </x-status-badge>
            </div>
            <flux:subheading size="lg" class="mt-1">
                @if($project->client_name)
                    {{ $project->client_name }}@if($project->client_email) ({{ $project->client_email }})@endif
                @else
                    Client non défini
                @endif
                <span class="mx-1 text-zinc-800/30 dark:text-white/20">•</span>
                Créé le {{ $project->created_at->format('d/m/Y') }}
            </flux:subheading>
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

    <flux:card variant="soft" class="p-0">
        <div class="flex gap-1 overflow-x-auto rounded-t-xl border-b border-zinc-800/5 p-2 dark:border-white/10">
            @foreach([
                'overview' => ['label' => "Vue d'ensemble", 'icon' => 'eye'],
                'features' => ['label' => 'Fonctionnalités', 'icon' => 'squares-2x2'],
                'external-costs' => ['label' => 'Coûts externes', 'icon' => 'banknotes'],
                'adjustments' => ['label' => 'Ajustements', 'icon' => 'adjustments-horizontal'],
            ] as $tab => $item)
                <button
                    wire:click="setActiveTab('{{ $tab }}')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3.5 py-2 text-sm font-medium transition-colors',
                        'bg-white text-zinc-800 shadow-xs dark:bg-white/10 dark:text-white' => $activeTab === $tab,
                        'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white' => $activeTab !== $tab,
                    ])
                >
                    <flux:icon :icon="$item['icon']" variant="micro" class="size-4" />
                    {{ $item['label'] }}
                    @php $count = $project->{$tab === 'overview' ? 'features' : match($tab) { 'external-costs' => 'externalCosts', 'adjustments' => 'pricingAdjustments', default => 'features' }}->count(); @endphp
                    @if($tab !== 'overview' && $count > 0)
                        <span class="rounded-full bg-zinc-800/5 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                            {{ $count }}
                        </span>
                    @endif
                </button>
            @endforeach

            @if($project->quotes->isNotEmpty())
                <button
                    wire:click="setActiveTab('quotes')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3.5 py-2 text-sm font-medium transition-colors',
                        'bg-white text-zinc-800 shadow-xs dark:bg-white/10 dark:text-white' => $activeTab === 'quotes',
                        'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white' => $activeTab !== 'quotes',
                    ])
                >
                    <flux:icon icon="document-text" variant="micro" class="size-4" />
                    Devis
                    <span class="rounded-full bg-zinc-800/5 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                        {{ $project->quotes->count() }}
                    </span>
                </button>
            @endif

            @if($project->aiAnalyses->isNotEmpty())
                <button
                    wire:click="setActiveTab('ai')"
                    @class([
                        'inline-flex items-center gap-2 whitespace-nowrap rounded-lg px-3.5 py-2 text-sm font-medium transition-colors',
                        'bg-white text-zinc-800 shadow-xs dark:bg-white/10 dark:text-white' => $activeTab === 'ai',
                        'text-zinc-500 hover:text-zinc-800 dark:text-zinc-400 dark:hover:text-white' => $activeTab !== 'ai',
                    ])
                >
                    <flux:icon icon="sparkles" variant="micro" class="size-4" />
                    Analyse IA
                    <span class="rounded-full bg-zinc-800/5 px-2 py-0.5 text-xs font-medium text-zinc-500 dark:bg-white/10 dark:text-zinc-300">
                        {{ $project->aiAnalyses->count() }}
                    </span>
                </button>
            @endif
        </div>

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
    </flux:card>

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