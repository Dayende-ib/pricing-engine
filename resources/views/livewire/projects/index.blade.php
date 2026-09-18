<div class="space-y-8">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <flux:heading size="xl">Projets</flux:heading>
            <flux:subheading size="lg">Gérez vos projets et estimations</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="w-full sm:w-auto">
            Nouveau projet
        </flux:button>
    </div>

    <flux:card variant="soft" class="p-0">
        <div class="flex flex-col gap-4 border-b border-zinc-800/5 p-5 sm:flex-row sm:items-center sm:p-6 dark:border-white/10">
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

        @if($projects->isEmpty())
            <div class="flex flex-col items-center justify-center px-6 py-16 text-center">
                <div class="flex size-20 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                    <flux:icon icon="folder" variant="outline" class="size-10 text-zinc-400" />
                </div>
                <flux:heading class="mt-5">{{ $search ? 'Aucun résultat' : 'Aucun projet' }}</flux:heading>
                <flux:text class="mt-1 max-w-md">
                    {{ $search ? 'Essayez de modifier votre recherche ou de changer les filtres.' : 'Commencez par créer votre premier projet pour organiser vos estimations.' }}
                </flux:text>
                @empty($search)
                    <flux:button variant="primary" icon="plus" href="{{ route('projects.create') }}" wire:navigate class="mt-6">
                        Créer mon premier projet
                    </flux:button>
                @endempty
            </div>
        @else
            <div class="px-5 sm:px-6">
                <flux:table :paginate="$projects">
                <flux:table.columns>
                    <flux:table.column class="w-64">Projet</flux:table.column>
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
                                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                                        <flux:icon icon="folder" name="folder" variant="outline" class="size-5 text-zinc-500" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-zinc-800 dark:text-white">{{ $project->name }}</div>
                                        <div class="truncate text-sm text-zinc-400">{{ Str::limit($project->description ?? '', 60) }}</div>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell variant="strong" class="hidden md:table-cell">
                                @if($project->client_name)
                                    <div class="text-zinc-800 dark:text-white">{{ $project->client_name }}</div>
                                    @if($project->client_email)
                                        <div class="text-sm text-zinc-400">{{ $project->client_email }}</div>
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
                                    <div class="font-medium text-zinc-800 dark:text-white">
                                        {{ number_format($project->quotes->first()->total, 0, ',', ' ') }} XOF
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
                                <div class="text-zinc-700 dark:text-zinc-200">{{ $project->created_at->format('d/m/Y') }}</div>
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
    </flux:card>
</div>