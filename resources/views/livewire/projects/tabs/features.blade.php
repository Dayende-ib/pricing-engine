<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading>Fonctionnalités</flux:heading>
            <flux:text size="sm" class="mt-0.5 text-zinc-500 dark:text-zinc-400">Estimez le travail à effectuer pour chaque fonctionnalité</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openFeatureModal">
            Ajouter
        </flux:button>
    </div>

    @if($project->features->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                <flux:icon icon="squares-2x2" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            </div>
            <flux:heading class="mt-4">Aucune fonctionnalité</flux:heading>
            <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">Ajoutez des fonctionnalités pour estimer le projet.</flux:text>

            <flux:button variant="primary" icon="plus" wire:click="openFeatureModal" class="mt-6">
                Ajouter la première fonctionnalité
            </flux:button>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200/60 dark:border-white/[0.06]">
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
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-teal-50 dark:bg-teal-500/10">
                                        <flux:icon icon="squares-2x2" variant="micro" class="size-4 text-teal-600 dark:text-teal-400" />
                                    </div>
                                    <div class="min-w-0">
                                        <div class="truncate font-medium text-zinc-900 dark:text-[#f0f0f2]">{{ $feature->name }}</div>
                                        @if($feature->priority !== 'normal')
                                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">
                                                Priorité
                                                @if($feature->priority === 'critical') critique @elseif($feature->priority === 'high') haute @elseif($feature->priority === 'low') basse @endif
                                            </flux:text>
                                        @endif
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="hidden lg:table-cell">
                                <div class="max-w-xs truncate text-zinc-600 dark:text-zinc-300">
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
                                <div class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($feature->getTotalHours(), 1) }} h</div>
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
        </div>

        {{-- Summary Stats --}}
        @php
            $totalHours = $project->features->sum(fn ($f) => $f->estimated_hours * $f->quantity);
        @endphp
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div class="rounded-xl border border-zinc-200/60 bg-white p-4 text-center dark:border-white/[0.06] dark:bg-surface">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Fonctionnalités</div>
                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-[#f0f0f2]">{{ $project->features->count() }}</div>
            </div>
            <div class="rounded-xl border border-zinc-200/60 bg-white p-4 text-center dark:border-white/[0.06] dark:bg-surface">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-zinc-400">Heures totales</div>
                <div class="mt-1 text-2xl font-bold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($totalHours, 1) }} h</div>
            </div>
            <div class="rounded-xl border border-teal-200/60 bg-teal-50 p-4 text-center dark:border-teal-500/20 dark:bg-teal-500/10 dark:shadow-[0_0_12px_-3px_rgba(45,212,191,0.1)]">
                <div class="text-[11px] font-semibold uppercase tracking-widest text-teal-600 dark:text-teal-400">Coût estimé</div>
                <div class="mt-1 text-2xl font-bold text-teal-600 dark:text-teal-400">
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
