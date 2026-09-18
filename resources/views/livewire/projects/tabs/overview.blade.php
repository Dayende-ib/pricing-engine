@php
    $projectTypeLabels = [
        'web_app' => 'Application Web',
        'mobile_app' => 'Application Mobile',
        'api' => 'API / Backend',
        'ecommerce' => 'Site E-commerce',
        'dashboard' => 'Dashboard / Admin',
        'landing_page' => 'Landing Page',
        'other' => 'Autre',
    ];
@endphp

<div class="grid grid-cols-1 gap-6 md:grid-cols-3">
    <div class="space-y-6 md:col-span-2">
        <flux:card variant="soft">
            <flux:heading>Informations générales</flux:heading>

            <dl class="mt-5 divide-y divide-zinc-800/5 dark:divide-white/10">
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Statut</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        <x-status-badge :status="$project->status->value">
                            {{ $project->status->label() }}
                        </x-status-badge>
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Type</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        <x-status-badge :status="$project->project_type">
                            {{ $projectTypeLabels[$project->project_type] ?? $project->project_type }}
                        </x-status-badge>
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Complexité</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        @if($project->complexity instanceof \App\Enums\ComplexityLevel)
                            <x-status-badge :status="$project->complexity->value">{{ $project->complexity->label() }}</x-status-badge>
                        @else
                            {{ $project->complexity }}
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Risque</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        @if($project->risk_level instanceof \App\Enums\RiskLevel)
                            <x-status-badge :status="$project->risk_level->value">{{ $project->risk_level->label() }}</x-status-badge>
                        @else
                            {{ $project->risk_level }}
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Date limite</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        @if($project->deadline)
                            {{ $project->deadline->format('d/m/Y') }}
                        @else
                            <span class="text-zinc-400">Non définie</span>
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-400">Profil tarifaire</dt>
                    <dd class="text-sm font-medium text-zinc-800 sm:col-span-2 dark:text-white">
                        @if($project->pricingProfile?->name)
                            {{ $project->pricingProfile->name }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </flux:card>

        @if($project->description)
            <flux:card variant="soft">
                <flux:heading>Description</flux:heading>
                <flux:text class="mt-3 whitespace-pre-wrap">{{ $project->description }}</flux:text>
            </flux:card>
        @endif
    </div>

    <div class="space-y-6">
        <flux:card variant="soft">
            <flux:heading>Tarification</flux:heading>

            @if($pricingResult)
                <div class="mt-5 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <flux:text>Heures totales</flux:text>
                        <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($pricingResult->totalHours, 1) }} h</div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <flux:text>Coût développement</flux:text>
                        <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($pricingResult->baseDevelopmentCost, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                    </div>
                    @if($pricingResult->externalCostsTotal > 0)
                        <div class="flex items-center justify-between text-sm">
                            <flux:text>Coûts externes</flux:text>
                            <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($pricingResult->externalCostsTotal, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif
                    @if($pricingResult->adjustmentsTotal > 0)
                        <div class="flex items-center justify-between text-sm">
                            <flux:text>Ajustements</flux:text>
                            <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($pricingResult->adjustmentsTotal, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif
                    @if($pricingResult->riskReserve > 0)
                        <div class="flex items-center justify-between text-sm">
                            <flux:text>Réserve risque ({{ number_format($project->pricingProfile->default_risk_reserve * 100, 0) }} %)</flux:text>
                            <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($pricingResult->riskReserve, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif

                    <flux:separator variant="subtle" />

                    <div class="flex items-center justify-between">
                        <flux:heading>Coût avant marge</flux:heading>
                        <flux:heading class="text-zinc-800 dark:text-white">{{ number_format($pricingResult->costBeforeMargin, 0, ',', ' ') }} {{ $pricingResult->currency }}</flux:heading>
                    </div>

                    <flux:separator variant="subtle" />

                    <div class="grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-zinc-800/5 p-3.5 text-center dark:bg-white/10">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Plancher</div>
                            <div class="mt-1 text-sm font-bold text-zinc-800 sm:text-base dark:text-white">{{ number_format($pricingResult->floorPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-zinc-400">Marge {{ number_format($project->pricingProfile->minimum_margin * 100, 0) }} %</div>
                        </div>
                        <div class="rounded-xl bg-indigo-500/10 p-3.5 text-center ring-1 ring-indigo-500/20">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-indigo-500">Cible</div>
                            <div class="mt-1 text-sm font-bold text-indigo-500 sm:text-base">{{ number_format($pricingResult->targetPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-indigo-500/60">Marge {{ number_format($project->pricingProfile->target_margin * 100, 0) }} %</div>
                        </div>
                        <div class="rounded-xl bg-zinc-800/5 p-3.5 text-center dark:bg-white/10">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Premium</div>
                            <div class="mt-1 text-sm font-bold text-zinc-800 sm:text-base dark:text-white">{{ number_format($pricingResult->premiumPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-zinc-400">Marge {{ number_format($project->pricingProfile->premium_margin * 100, 0) }} %</div>
                        </div>
                    </div>
                </div>
            @else
                <flux:callout icon="information-circle" variant="subtle" class="mt-5">
                    <flux:callout.text>
                        Cliquez sur « Calculer le prix » pour générer l'estimation.
                    </flux:callout.text>
                </flux:callout>
            @endif
        </flux:card>

        <flux:card variant="soft">
            <flux:heading>Budget client</flux:heading>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <flux:text>Budget min</flux:text>
                    <div class="font-medium text-zinc-800 dark:text-white">
                        @if($project->budget_min)
                            {{ number_format($project->budget_min, 0, ',', ' ') }} {{ $project->currency }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <flux:text>Budget max</flux:text>
                    <div class="font-medium text-zinc-800 dark:text-white">
                        @if($project->budget_max)
                            {{ number_format($project->budget_max, 0, ',', ' ') }} {{ $project->currency }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </div>
                </div>
            </dl>
        </flux:card>
    </div>
</div>