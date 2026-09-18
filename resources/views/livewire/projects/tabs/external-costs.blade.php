<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading>Coûts externes</flux:heading>
            <flux:subheading size="sm">Hébergement, licences, API récurrentes...</flux:subheading>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openExternalCostModal">
            Ajouter
        </flux:button>
    </div>

    @if($project->externalCosts->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="banknotes" variant="outline" class="size-8 text-zinc-400" />
            </div>
            <flux:heading class="mt-4">Aucun coût externe</flux:heading>
            <flux:text class="mt-1 max-w-md">Ajoutez les coûts récurrents (hébergement, API, licences...).</flux:text>

            <flux:button variant="primary" icon="plus" wire:click="openExternalCostModal" class="mt-6">
                Ajouter le premier coût
            </flux:button>
        </div>
    @else
        @php
            $months = $project->deadline ? max(1, now()->diffInMonths($project->deadline)) : 1;
        @endphp

        <flux:table class="text-sm">
            <flux:table.columns>
                <flux:table.column class="w-64">Coût</flux:table.column>
                <flux:table.column class="hidden lg:table-cell">Description</flux:table.column>
                <flux:table.column>Fréquence</flux:table.column>
                <flux:table.column align="center">Qté</flux:table.column>
                <flux:table.column>Montant</flux:table.column>
                <flux:table.column>Total ({{ $months }} mois)</flux:table.column>
                <flux:table.column align="end">Actions</flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @foreach($project->externalCosts as $cost)
                    @php
                        $total = match($cost->frequency->value) {
                            'one_time' => $cost->amount * $cost->quantity,
                            'monthly' => $cost->amount * $cost->quantity * $months,
                            'yearly' => $cost->amount * $cost->quantity * ceil($months / 12),
                        };
                    @endphp
                    <flux:table.row>
                        <flux:table.cell variant="strong">
                            <div class="flex items-center gap-3">
                                <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-zinc-800/5 dark:bg-white/10">
                                    <flux:icon icon="banknotes" variant="micro" class="size-4 text-zinc-500" />
                                </div>
                                <div class="truncate text-zinc-800 dark:text-white">{{ $cost->name }}</div>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="hidden lg:table-cell">
                            <div class="max-w-xs truncate">
                                @if($cost->description)
                                    {{ $cost->description }}
                                @else
                                    <span class="text-zinc-400">—</span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <x-status-badge :status="$cost->frequency->value">{{ $cost->frequency->label() }}</x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell align="center">
                            <x-status-badge color="zinc">{{ $cost->quantity }}</x-status-badge>
                        </flux:table.cell>
                        <flux:table.cell>{{ number_format($cost->amount, 0, ',', ' ') }} {{ $project->currency }}</flux:table.cell>
                        <flux:table.cell variant="strong">{{ number_format($total, 0, ',', ' ') }} {{ $project->currency }}</flux:table.cell>
                        <flux:table.cell align="end">
                            <div class="flex items-center justify-end gap-1">
                                <flux:button variant="ghost" icon="pencil" size="sm" wire:click="openExternalCostModal({{ $cost->id }})">
                                    Modifier
                                </flux:button>
                                <flux:button variant="ghost" icon="trash" size="sm" color="red"
                                    wire:click="deleteExternalCost({{ $cost->id }})"
                                    wire:confirm="Supprimer ce coût externe ?">
                                    Supprimer
                                </flux:button>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforeach
            </flux:table.rows>
        </flux:table>

        @php
            $grandTotal = $project->externalCosts->sum(function ($cost) use ($months) {
                return match($cost->frequency->value) {
                    'one_time' => $cost->amount * $cost->quantity,
                    'monthly' => $cost->amount * $cost->quantity * $months,
                    'yearly' => $cost->amount * $cost->quantity * ceil($months / 12),
                };
            });
        @endphp

        <div class="flex justify-end border-t border-zinc-800/5 pt-5 dark:border-white/10">
            <div class="rounded-xl bg-indigo-500/10 px-5 py-4 text-right ring-1 ring-indigo-500/20">
                <div class="text-xs uppercase tracking-widest text-indigo-500">Total coûts externes ({{ $months }} mois)</div>
                <div class="mt-1 text-2xl font-bold text-indigo-500">{{ number_format($grandTotal, 0, ',', ' ') }} {{ $project->currency }}</div>
            </div>
        </div>
    @endif
</div>