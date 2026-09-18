<div class="space-y-6">
    {{-- Page Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Projets</flux:heading>
            <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">Gérez vos projets et estimations</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="w-full sm:w-auto">
            Nouveau projet
        </flux:button>
    </div>

    {{-- Search & Filters --}}
    <div class="rounded-2xl border border-zinc-200/60 bg-white p-4 dark:border-white/[0.06] dark:bg-surface">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
            <flux:input
                icon="magnifying-glass"
                wire:model.debounce.300ms="search"
                placeholder="Rechercher un projet..."
                class="max-w-md flex-1"
            />

            <div class="flex flex-wrap gap-3">
                <flux:select wire:model="statusFilter" class="w-full sm:w-52">
                    <flux:select.option value="">Tous les statuts</flux:select.option>
                    @foreach(\App\Enums\ProjectStatus::cases() as $status)
                        <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                    @endforeach
                </flux:select>

                <flux:select wire:model="perPage" class="w-full sm:w-40">
                    <flux:select.option value="10">10 par page</flux:select.option>
                    <flux:select.option value="25">25 par page</flux:select.option>
                    <flux:select.option value="50">50 par page</flux:select.option>
                </flux:select>
            </div>
        </div>
    </div>

    {{-- Projects Table --}}
    <div class="overflow-hidden rounded-2xl border border-zinc-200/60 bg-white dark:border-white/[0.06] dark:bg-surface">
        @if($projects->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                    <flux:icon icon="folder" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
                </div>
                <flux:heading class="mt-4">{{ $search ? 'Aucun résultat' : 'Aucun projet' }}</flux:heading>
                <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">
                    {{ $search ? 'Essayez de modifier votre recherche ou de changer les filtres.' : 'Commencez par créer votre premier projet pour organiser vos estimations.' }}
                </flux:text>
                @empty($search)
                    <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="mt-6">
                        Créer mon premier projet
                    </flux:button>
                @endempty
            </div>
        @else
            <div class="overflow-x-auto">
                <flux:table :paginate="$projects">
                    <flux:table.columns>
                        <flux:table.column class="w-72">Projet</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Client</flux:table.column>
                        <flux:table.column class="hidden lg:table-cell">Type</flux:table.column>
                        <flux:table.column>Statut</flux:table.column>
                        <flux:table.column class="hidden sm:table-cell">Prix</flux:table.column>
                        <flux:table.column class="hidden md:table-cell">Créé le</flux:table.column>
                        <flux:table.column align="end">Actions</flux:table.column>
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($projects as $project)
                            <flux:table.row>
                                <flux:table.cell>
                                    <div class="flex items-center gap-3">
                                        <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-500/10">
                                            <flux:icon icon="folder" variant="outline" class="size-5 text-teal-600 dark:text-teal-400" />
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ $project->name }}</div>
                                            <div class="truncate text-sm text-zinc-500 dark:text-zinc-400">{{ Str::limit($project->description ?? '', 60) }}</div>
                                        </div>
                                    </div>
                                </flux:table.cell>
                                <flux:table.cell class="hidden md:table-cell">
                                    @if($project->client_name)
                                        <div class="font-medium text-zinc-800 dark:text-zinc-200">{{ $project->client_name }}</div>
                                        @if($project->client_email)
                                            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $project->client_email }}</div>
                                        @endif
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
                                        <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">
                                            {{ number_format($project->quotes->first()->total, 0, ',', ' ') }} <span class="text-xs font-normal text-zinc-400">XOF</span>
                                        </div>
                                        <flux:text size="sm" :color="match($project->quotes->first()->status) {
                                            'accepted' => 'emerald',
                                            'sent' => 'blue',
                                            'viewed' => 'indigo',
                                            default => null,
                                        }">
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
                                <flux:table.cell class="hidden md:table-cell">
                                    <div class="text-zinc-700 dark:text-zinc-300">{{ $project->created_at->format('d/m/Y') }}</div>
                                    <div class="text-sm text-zinc-400">{{ $project->created_at->diffForHumans() }}</div>
                                </flux:table.cell>
                                <flux:table.cell align="end">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button variant="ghost" icon="eye" size="sm" href="{{ route('projects.show', $project) }}" wire:navigate>
                                            Voir
                                        </flux:button>
                                        <flux:button variant="ghost" icon="pencil" size="sm" href="{{ route('projects.edit', $project) }}" wire:navigate>
                                            Modifier
                                        </flux:button>
                                    </div>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
    </div>
</div>
