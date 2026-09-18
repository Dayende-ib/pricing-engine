<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Tableau de bord</flux:heading>
            <flux:subheading size="lg">Vue d'ensemble de votre activité</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="w-full sm:w-auto">
            Nouveau projet
        </flux:button>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="folder" variant="outline" class="size-5 text-indigo-500" />
            </div>
            <div class="min-w-0">
                <flux:text>Projets ce mois</flux:text>
                <flux:heading size="xl" class="mt-1">{{ $projectsThisMonth }}</flux:heading>
            </div>
        </flux:card>

        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="document-text" variant="outline" class="size-5 text-emerald-500" />
            </div>
            <div class="min-w-0">
                <flux:text>Devis générés</flux:text>
                <flux:heading size="xl" class="mt-1">{{ $quotesGenerated }}</flux:heading>
            </div>
        </flux:card>

        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="banknotes" variant="outline" class="size-5 text-purple-500" />
            </div>
            <div class="min-w-0">
                <flux:text>CA potentiel</flux:text>
                <flux:heading size="xl" class="mt-1">{{ number_format($potentialRevenue, 0, ',', ' ') }}</flux:heading>
                <flux:text size="sm">XOF</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="check-badge" variant="outline" class="size-5 text-teal-500" />
            </div>
            <div class="min-w-0">
                <flux:text>CA accepté</flux:text>
                <flux:heading size="xl" class="mt-1">{{ number_format($acceptedRevenue, 0, ',', ' ') }}</flux:heading>
                <flux:text size="sm">XOF</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="calculator" variant="outline" class="size-5 text-amber-500" />
            </div>
            <div class="min-w-0">
                <flux:text>Prix moyen</flux:text>
                <flux:heading size="xl" class="mt-1">{{ number_format($averageProjectPrice, 0, ',', ' ') }}</flux:heading>
                <flux:text size="sm">XOF</flux:text>
            </div>
        </flux:card>

        <flux:card class="flex items-start gap-4">
            <div class="flex size-11 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="chart-bar" variant="outline" class="size-5 text-blue-500" />
            </div>
            <div class="min-w-0">
                <flux:text>Marge moyenne</flux:text>
                <flux:heading size="xl" class="mt-1">{{ number_format($averageMargin, 1) }} %</flux:heading>
            </div>
        </flux:card>
    </div>

    <flux:card variant="soft" class="p-0">
        <div class="flex items-center justify-between gap-4 border-b border-zinc-800/5 p-5 sm:p-6 dark:border-white/10">
            <div>
                <flux:heading>Projets récents</flux:heading>
                <flux:text size="sm">Vos 5 derniers projets</flux:text>
            </div>

            <flux:button variant="ghost" icon-trailing="arrow-right" href="{{ route('projects.index') }}" wire:navigate class="text-indigo-500">
                Voir tout
            </flux:button>
        </div>

        @if($recentProjects->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                    <flux:icon icon="folder" variant="outline" class="size-8 text-zinc-400" />
                </div>
                <flux:heading class="mt-4">Aucun projet</flux:heading>
                <flux:text class="mt-1 max-w-md">Commencez par créer votre premier projet pour organiser vos estimations.</flux:text>
                <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="mt-6">
                    Créer mon premier projet
                </flux:button>
            </div>
        @else
            <div class="px-5 sm:px-6">
                <flux:table class="text-sm">
                <flux:table.columns>
                    <flux:table.column>Projet</flux:table.column>
                    <flux:table.column class="hidden md:table-cell">Client</flux:table.column>
                    <flux:table.column class="hidden lg:table-cell">Type</flux:table.column>
                    <flux:table.column>Statut</flux:table.column>
                    <flux:table.column class="hidden sm:table-cell">Prix</flux:table.column>
                    <flux:table.column align="end">Actions</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @foreach($recentProjects as $project)
                        <flux:table.row>
                            <flux:table.cell class="max-w-xs">
                                <div class="truncate font-medium text-zinc-800 dark:text-white">{{ $project->name }}</div>
                                <div class="truncate text-sm text-zinc-400">{{ Str::limit($project->description ?? '', 50) }}</div>
                            </flux:table.cell>
                            <flux:table.cell variant="strong" class="hidden md:table-cell">
                                @if($project->client_name)
                                    {{ $project->client_name }}
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell class="hidden lg:table-cell">
                                <x-status-badge :status="$project->project_type">{{ $project->project_type }}</x-status-badge>
                            </flux:table.cell>
                            <flux:table.cell>
                                <x-status-badge :status="$project->status">
                                    {{ \App\Enums\ProjectStatus::tryFrom($project->status)?->label() ?? $project->status }}
                                </x-status-badge>
                            </flux:table.cell>
                            <flux:table.cell class="hidden sm:table-cell">
                                @if($project->quotes->isNotEmpty())
                                    <div class="font-medium text-zinc-800 dark:text-white">
                                        {{ number_format($project->quotes->first()->total, 0, ',', ' ') }} XOF
                                    </div>
                                    <flux:text size="sm" class="{{ in_array($project->quotes->first()->status, ['accepted', 'sent', 'viewed']) ? '' : 'text-zinc-400' }}">
                                        {{ match($project->quotes->first()->status) {
                                            'accepted' => 'Accepté',
                                            'sent' => 'Envoyé',
                                            'viewed' => 'Consulté',
                                            default => 'Brouillon',
                                        } }}
                                    </flux:text>
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell align="end">
                                <flux:button variant="ghost" icon="eye" size="sm" href="{{ route('projects.show', $project) }}" wire:navigate>
                                    Voir
                                </flux:button>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforeach
                </flux:table.rows>
            </flux:table>
            </div>
        @endif
    </flux:card>
</div>