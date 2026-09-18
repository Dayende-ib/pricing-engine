<div class="space-y-8">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Tableau de bord</flux:heading>
            <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">Vue d'ensemble de votre activité</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="w-full sm:w-auto">
            Nouveau projet
        </flux:button>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        {{-- Projects this month --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-teal-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-teal-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(45,212,191,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-500/10">
                    <flux:icon icon="folder" variant="outline" class="size-5 text-teal-600 dark:text-teal-400" />
                </div>
                <span class="rounded-full bg-teal-50 px-2 py-0.5 text-xs font-medium text-teal-600 dark:bg-teal-500/10 dark:text-teal-400">Mois</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ $projectsThisMonth }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Projets ce mois</div>
            </div>
        </div>

        {{-- Quotes generated --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-emerald-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-emerald-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(16,185,129,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-500/10">
                    <flux:icon icon="document-text" variant="outline" class="size-5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-medium text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">Mois</span>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ $quotesGenerated }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Devis générés</div>
            </div>
        </div>

        {{-- Potential revenue --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-violet-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-violet-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(139,92,246,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-500/10">
                    <flux:icon icon="banknotes" variant="outline" class="size-5 text-violet-600 dark:text-violet-400" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($potentialRevenue, 0, ',', ' ') }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">CA potentiel <span class="text-xs text-zinc-400 dark:text-zinc-500">XOF</span></div>
            </div>
        </div>

        {{-- Accepted revenue --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-teal-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-teal-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(45,212,191,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-500/10">
                    <flux:icon icon="check-badge" variant="outline" class="size-5 text-teal-600 dark:text-teal-400" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($acceptedRevenue, 0, ',', ' ') }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">CA accepté <span class="text-xs text-zinc-400 dark:text-zinc-500">XOF</span></div>
            </div>
        </div>

        {{-- Average price --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-amber-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-amber-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(245,158,11,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-amber-50 dark:bg-amber-500/10">
                    <flux:icon icon="calculator" variant="outline" class="size-5 text-amber-600 dark:text-amber-400" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($averageProjectPrice, 0, ',', ' ') }}</div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Prix moyen <span class="text-xs text-zinc-400 dark:text-zinc-500">XOF</span></div>
            </div>
        </div>

        {{-- Average margin --}}
        <div class="group relative overflow-hidden rounded-2xl border border-zinc-200/60 bg-white p-5 transition-all duration-200 hover:border-blue-200 hover:shadow-sm dark:border-white/[0.06] dark:bg-surface dark:transition-all dark:hover:border-blue-500/30 dark:hover:shadow-[0_0_20px_-5px_rgba(59,130,246,0.15)]">
            <div class="flex items-start justify-between">
                <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-blue-50 dark:bg-blue-500/10">
                    <flux:icon icon="chart-bar" variant="outline" class="size-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-3">
                <div class="text-2xl font-bold tracking-tight text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($averageMargin, 1) }}<span class="text-base font-medium text-zinc-400">%</span></div>
                <div class="text-sm text-zinc-500 dark:text-zinc-400">Marge moyenne</div>
            </div>
        </div>
    </div>

    {{-- Recent Projects --}}
    <div class="overflow-hidden rounded-2xl border border-zinc-200/60 bg-white dark:border-white/[0.06] dark:bg-surface">
        <div class="flex items-center justify-between gap-4 border-b border-zinc-100 px-6 py-5 dark:border-white/[0.06]">
            <div>
                <flux:heading>Projets récents</flux:heading>
                <flux:text size="sm" class="mt-0.5 text-zinc-500 dark:text-zinc-400">Vos 5 derniers projets</flux:text>
            </div>

            <flux:button variant="ghost" icon-trailing="arrow-right" href="{{ route('projects.index') }}" wire:navigate class="text-teal-600 hover:text-teal-700 dark:text-teal-400 dark:hover:text-teal-300">
                Voir tout
            </flux:button>
        </div>

        @if($recentProjects->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                    <flux:icon icon="folder" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
                </div>
                <flux:heading class="mt-4">Aucun projet</flux:heading>
                <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">Commencez par créer votre premier projet pour organiser vos estimations.</flux:text>
                <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="mt-6">
                    Créer mon premier projet
                </flux:button>
            </div>
        @else
            <div class="divide-y divide-zinc-100 dark:divide-white/[0.06]">
                @foreach($recentProjects as $project)
                    <a href="{{ route('projects.show', $project) }}" wire:navigate class="flex items-center gap-4 px-6 py-4 transition-colors hover:bg-zinc-50 dark:hover:bg-white/[0.03]">
                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-500/10">
                            <flux:icon icon="folder" variant="outline" class="size-5 text-teal-600 dark:text-teal-400" />
                        </div>

                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <div class="truncate font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ $project->name }}</div>
                                <x-status-badge :status="$project->status">
                                    {{ \App\Enums\ProjectStatus::tryFrom($project->status)?->label() ?? $project->status }}
                                </x-status-badge>
                            </div>
                            <div class="mt-0.5 flex items-center gap-3 text-sm text-zinc-500 dark:text-zinc-400">
                                @if($project->client_name)
                                    <span class="truncate">{{ $project->client_name }}</span>
                                    <span class="text-zinc-300 dark:text-zinc-600">·</span>
                                @endif
                                <span class="truncate">{{ Str::limit($project->description ?? '', 60) }}</span>
                            </div>
                        </div>

                        <div class="hidden shrink-0 text-right sm:block">
                            @if($project->quotes->isNotEmpty())
                                <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">
                                    {{ number_format($project->quotes->first()->total, 0, ',', ' ') }} <span class="text-xs text-zinc-400">XOF</span>
                                </div>
                            @else
                                <span class="text-sm text-zinc-400">—</span>
                            @endif
                        </div>

                        <flux:icon icon="chevron-right" variant="micro" class="size-4 shrink-0 text-zinc-300 dark:text-zinc-600" />
                    </a>
                @endforeach
            </div>
        @endif
    </div>
</div>
