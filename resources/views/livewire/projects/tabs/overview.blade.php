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

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- General Info --}}
    <div class="space-y-6 lg:col-span-2">
        <div class="rounded-xl border border-zinc-200/60 bg-zinc-50/50 p-5 dark:border-white/[0.06] dark:bg-surface-raised">
            <flux:heading>Informations générales</flux:heading>

            <dl class="mt-4 divide-y divide-zinc-200/60 dark:divide-white/[0.06]">
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Statut</dt>
                    <dd class="text-sm sm:col-span-2">
                        <x-status-badge :status="$project->status->value">
                            {{ $project->status->label() }}
                        </x-status-badge>
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Type</dt>
                    <dd class="text-sm sm:col-span-2">
                        <x-status-badge :status="$project->project_type">
                            {{ $projectTypeLabels[$project->project_type] ?? $project->project_type }}
                        </x-status-badge>
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Complexité</dt>
                    <dd class="text-sm sm:col-span-2">
                        @if($project->complexity instanceof \App\Enums\ComplexityLevel)
                            <x-status-badge :status="$project->complexity->value">{{ $project->complexity->label() }}</x-status-badge>
                        @else
                            {{ $project->complexity }}
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Risque</dt>
                    <dd class="text-sm sm:col-span-2">
                        @if($project->risk_level instanceof \App\Enums\RiskLevel)
                            <x-status-badge :status="$project->risk_level->value">{{ $project->risk_level->label() }}</x-status-badge>
                        @else
                            {{ $project->risk_level }}
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Date limite</dt>
                    <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                        @if($project->deadline)
                            {{ $project->deadline->format('d/m/Y') }}
                        @else
                            <span class="text-zinc-400">Non définie</span>
                        @endif
                    </dd>
                </div>
                <div class="grid grid-cols-1 gap-1 py-3 sm:grid-cols-3">
                    <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Profil tarifaire</dt>
                    <dd class="text-sm font-semibold text-zinc-900 sm:col-span-2 dark:text-[#f0f0f2]">
                        @if($project->pricingProfile?->name)
                            {{ $project->pricingProfile->name }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </dd>
                </div>
            </dl>
        </div>

        @if($project->description)
            <div class="rounded-xl border border-zinc-200/60 bg-zinc-50/50 p-5 dark:border-white/[0.06] dark:bg-surface-raised">
                <flux:heading>Description</flux:heading>
                <flux:text class="mt-3 whitespace-pre-wrap text-zinc-600 dark:text-zinc-300">{{ $project->description }}</flux:text>
            </div>
        @endif
    </div>

    {{-- Pricing Sidebar --}}
    <div class="space-y-6">
        {{-- Pricing Result --}}
        <div class="rounded-xl border border-zinc-200/60 bg-zinc-50/50 p-5 dark:border-white/[0.06] dark:bg-surface-raised">
            <flux:heading>Tarification</flux:heading>

            @if($pricingResult)
                <div class="mt-4 space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Heures totales</span>
                        <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->totalHours, 1) }} h</div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400">Coût développement</span>
                        <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->baseDevelopmentCost, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                    </div>
                    @if($pricingResult->externalCostsTotal > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Coûts externes</span>
                            <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->externalCostsTotal, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif
                    @if($pricingResult->adjustmentsTotal > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Ajustements</span>
                            <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->adjustmentsTotal, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif
                    @if($pricingResult->riskReserve > 0)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-zinc-500 dark:text-zinc-400">Réserve risque ({{ number_format($project->pricingProfile->default_risk_reserve * 100, 0) }}%)</span>
                            <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->riskReserve, 0, ',', ' ') }} {{ $pricingResult->currency }}</div>
                        </div>
                    @endif

                    <div class="border-t border-zinc-200/60 pt-3 dark:border-white/[0.06]">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-semibold text-zinc-900 dark:text-[#f0f0f2]">Coût avant marge</span>
                            <span class="text-sm font-bold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->costBeforeMargin, 0, ',', ' ') }} {{ $pricingResult->currency }}</span>
                        </div>
                    </div>

                    {{-- Price Tiers --}}
                    <div class="grid grid-cols-3 gap-2 pt-2">
                        <div class="rounded-xl bg-zinc-100 p-3 text-center dark:bg-white/[0.04]">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Plancher</div>
                            <div class="mt-1 text-sm font-bold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->floorPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-zinc-400">Marge {{ number_format($project->pricingProfile->minimum_margin * 100, 0) }}%</div>
                        </div>
                        <div class="rounded-xl bg-teal-50 p-3 text-center ring-1 ring-teal-200 dark:bg-teal-500/10 dark:ring-teal-500/20 dark:shadow-[0_0_12px_-3px_rgba(45,212,191,0.15)]">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-teal-600 dark:text-teal-400">Cible</div>
                            <div class="mt-1 text-sm font-bold text-teal-600 dark:text-teal-400">{{ number_format($pricingResult->targetPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-teal-500/60">Marge {{ number_format($project->pricingProfile->target_margin * 100, 0) }}%</div>
                        </div>
                        <div class="rounded-xl bg-zinc-100 p-3 text-center dark:bg-white/[0.04]">
                            <div class="text-[10px] font-semibold uppercase tracking-widest text-zinc-400">Premium</div>
                            <div class="mt-1 text-sm font-bold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($pricingResult->premiumPrice, 0, ',', ' ') }}</div>
                            <div class="text-[11px] text-zinc-400">Marge {{ number_format($project->pricingProfile->premium_margin * 100, 0) }}%</div>
                        </div>
                    </div>
                </div>
            @else
                <div class="mt-4 flex items-center gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-500/20 dark:bg-amber-500/10">
                    <flux:icon icon="information-circle" class="size-5 text-amber-600 dark:text-amber-400" />
                    <span class="text-sm text-amber-700 dark:text-amber-300">Cliquez sur « Calculer le prix » pour générer l'estimation.</span>
                </div>
            @endif
        </div>

        {{-- Client Budget --}}
        <div class="rounded-xl border border-zinc-200/60 bg-zinc-50/50 p-5 dark:border-white/[0.06] dark:bg-surface-raised">
            <flux:heading>Budget client</flux:heading>

            <dl class="mt-4 space-y-2 text-sm">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Budget min</span>
                    <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">
                        @if($project->budget_min)
                            {{ number_format($project->budget_min, 0, ',', ' ') }} {{ $project->currency }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-500 dark:text-zinc-400">Budget max</span>
                    <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">
                        @if($project->budget_max)
                            {{ number_format($project->budget_max, 0, ',', ' ') }} {{ $project->currency }}
                        @else
                            <span class="text-zinc-400">Non défini</span>
                        @endif
                    </div>
                </div>
            </dl>
        </div>
    </div>
</div>
