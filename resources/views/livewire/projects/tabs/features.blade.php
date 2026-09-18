<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading>Fonctionnalités</flux:heading>
            <flux:subheading size="sm">Estimez le travail à effectuer pour chaque fonctionnalité</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openFeatureModal">
            Ajouter
        </flux:button>
    </div>

    @if($project->features->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="squares-2x2" variant="outline" class="size-8 text-zinc-400" />
            </div>
            <flux:heading class="mt-4">Aucune fonctionnalité</flux:heading>
            <flux:text class="mt-1 max-w-md">Ajoutez des fonctionnalités pour estimer le projet.</flux:text>

            <flux:button variant="primary" icon="plus" wire:click="openFeatureModal" class="mt-6">
                Ajouter la première fonctionnalité
            </flux:button>
        </div>
    @else
        <flux:table class="text-sm">
            <flux:table.columns>
                <flux:table.column class="w-72">Fonctionnalité</flux:table.column>
                <flux:table.column class="hidden lg:table-cell">Description</flux:table.column>
                <flux:table.column align="center">Qté</flux:table.column>
                <flux:table.column>Heures</flux:table.column>
                <flux:table.column>Total</flux:table.column>
                <flux:table.column>Complexité</flux:table.column>
                <flux:table.column>Source</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($project->features as $feature)
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-800/5 dark:bg-white/10">
                                    <flux:icon icon="squares-2x2" variant="micro" class="size-4 text-zinc-500" />
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate text-zinc-800 dark:text-white">{{ $feature->name }}</div>
                                    @if($feature->priority !== 'normal')
                                        <flux:text size="sm">
                                            Priorité
                                            @if($feature->priority === 'critical') critique @elseif($feature->priority === 'high') haute @elseif($feature->priority === 'low') basse @endif
                                        </flux:text>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden lg:table-cell">
                            <div class="max-w-xs truncate">
                                @if($feature->description)
                                    {{ $feature->description }}
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <x-status-badge :status="$feature->quantity" color="zinc">{{ $feature->quantity }}</x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ number_format($feature->estimated_hours, 1) }} h</flux:table.cell>
                        <flux:table.cell variant="strong">
                            <div class="font-medium text-zinc-800 dark:text-white">{{ number_format($feature->getTotalHours(), 1) }} h</div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$feature->complexity->value">
                                {{ $feature->complexity->label() }}
                            </x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$feature->estimation_source->value">
                                {{ $feature->estimation_source->label() }}
                            </x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" icon="pencil" size="sm" wire:click="openFeatureModal({{ $feature->id }})">
                                    Modifier
                                </flux:button>
                                <flux:button variant="ghost" icon="trash" size="sm" color="red"
                                    wire:click="deleteFeature({{ $feature->id }})"
                                    wire:confirm="Supprimer cette fonctionnalité ?">
                                    Supprimer
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @php
            $totalHours = $project->features->sum(fn ($f) => $f->estimated_hours * $f->quantity);
        @endphp
        <div class="grid grid-cols-1 gap-4 border-t border-zinc-800/5 pt-5 sm:grid-cols-3 dark:border-white/10">
            <div class="rounded-xl bg-zinc-800/5 p-4 text-center dark:bg-white/10">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Fonctionnalités</div>
                <div class="mt-1 text-2xl font-bold text-zinc-800 dark:text-white">{{ $project->features->count() }}</div>
            </div>
            <div class="rounded-xl bg-zinc-800/5 p-4 text-center dark:bg-white/10">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Heures totales</div>
                <div class="mt-1 text-2xl font-bold text-zinc-800 dark:text-white">{{ number_format($totalHours, 1) }} h</div>
            </div>
            <div class="rounded-xl bg-zinc-800/5 p-4 text-center dark:bg-white/10">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Coût estimé</div>
                <div class="mt-1 text-2xl font-bold text-zinc-800 dark:text-white">
                    @if($project->pricingProfile)
                        {{ number_format($totalHours * $project->pricingProfile->hourly_rate, 0, ',', ' ') }} {{ $project->currency }}
                    @else
                        —
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>