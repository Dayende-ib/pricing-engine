<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading>Ajustements</flux:heading>
            <flux:subheading size="sm">Urgence, complexité technique, négociation...</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openAdjustmentModal">
            Ajouter
        </flux:button>
    </div>

    @if($project->pricingAdjustments->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="adjustments-horizontal" variant="outline" class="size-8 text-zinc-400" />
            </div>
            <flux:heading class="mt-4">Aucun ajustement</flux:heading>
            <flux:text class="mt-1 max-w-md">Ajoutez des ajustements pour l'urgence, la complexité, la négociation...</flux:text>

            <flux:button variant="primary" icon="plus" wire:click="openAdjustmentModal" class="mt-6">
                Ajouter le premier ajustement
            </flux:button>
        </div>
    @else
        @php
            $devCost = $project->features->sum(fn ($f) => $f->estimated_hours * $f->quantity) * ($project->pricingProfile->hourly_rate ?? 0);
        @endphp

        <flux:table class="text-sm">
            <flux:table.columns>
                <flux:table.column class="w-64">Ajustement</flux:table.column>
                <flux:table.column class="hidden lg:table-cell">Description</flux:table.column>
                <flux:table.column>Type</flux:table.column>
                <flux:table.column>Valeur</flux:table.column>
                <flux:table.column class="hidden sm:table-cell">Impact estimé</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($project->pricingAdjustments as $adj)
                    @php
                        $impact = $adj->type === 'percentage'
                            ? $devCost * ($adj->value / 100)
                            : $adj->value;
                    @endphp
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-800/5 dark:bg-white/10">
                                    <flux:icon
                                        :icon="$adj->type === 'percentage' ? 'percent' : 'banknotes'"
                                        variant="micro"
                                        class="size-4 text-zinc-500"
                                    />
                                </div>
                                <div class="min-w-0">
                                    <div class="truncate text-zinc-800 dark:text-white">{{ $adj->name }}</div>
                                    @if($adj->reason)
                                        <div class="truncate text-sm text-zinc-400">{{ $adj->reason }}</div>
                                    @endif
                                </div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden lg:table-cell">
                            <div class="max-w-xs truncate">
                                @if($adj->description)
                                    {{ $adj->description }}
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$adj->type->value">{{ $adj->type->label() }}</x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell variant="strong">
                            @if($adj->type === 'percentage')
                                {{ number_format($adj->value, 1) }} %
                            @else
                                {{ number_format($adj->value, 0, ',', ' ') }} {{ $project->currency }}
                            @endif
                        </flux:table.cell>
                        <flux:table.cell variant="strong" class="hidden sm:table-cell">
                            {{ number_format($impact, 0, ',', ' ') }} {{ $project->currency }}
                        </flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" icon="pencil" size="sm" wire:click="openAdjustmentModal({{ $adj->id }})">
                                    Modifier
                                </flux:button>
                                <flux:button variant="ghost" icon="trash" size="sm" color="red"
                                    wire:click="deleteAdjustment({{ $adj->id }})"
                                    wire:confirm="Supprimer cet ajustement ?">
                                    Supprimer
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @php
            $totalAdjustments = $project->pricingAdjustments->sum(function ($adj) use ($devCost) {
                return $adj->type === 'percentage'
                    ? $devCost * ($adj->value / 100)
                    : $adj->value;
            });
        @endphp

        <div class="flex justify-end border-t border-zinc-800/5 pt-5 dark:border-white/10">
            <div class="rounded-xl bg-zinc-800/5 px-5 py-4 text-right dark:bg-white/10">
                <div class="text-xs uppercase tracking-widest text-zinc-400">Total ajustements</div>
                <div class="mt-1 text-2xl font-bold text-zinc-800 dark:text-white">
                    {{ number_format($totalAdjustments, 0, ',', ' ') }} {{ $project->currency }}
                </div>
            </div>
        </div>
    @endif
</div>