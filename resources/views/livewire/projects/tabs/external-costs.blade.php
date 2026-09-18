<div class="space-y-6">
    <div class="flex items-center justify-between gap-4">
        <div>
            <flux:heading>Coûts externes</flux:heading>
            <flux:text size="sm" class="mt-0.5 text-zinc-500 dark:text-zinc-400">Hébergement, licences, API récurrentes...</flux:text>
        </div>

        <flux:button variant="primary" icon="plus" wire:click="openExternalCostModal">
            Ajouter
        </flux:button>
    </div>

    @if($project->externalCosts->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                <flux:icon icon="banknotes" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            </div>
            <flux:heading class="mt-4">Aucun coût externe</flux:heading>
            <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">Ajoutez les coûts récurrents (hébergement, API, licences...).</flux:text>

            <flux:button variant="primary" icon="plus" wire:click="openExternalCostModal" class="mt-6">
                Ajouter le premier coût
            </flux:button>
        </div>
    @else
        @php
            $months = $project->deadline ? max(1, now()->diffInMonths($project->deadline)) : 1;
        @endphp

        <div class="overflow-hidden rounded-xl border border-zinc-200/60 dark:border-white/[0.06]">
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
                                    <div class="flex size-9 shrink-0 items-center justify-center rounded-lg bg-amber-50 dark:bg-amber-500/10">
                                        <flux:icon icon="banknotes" variant="micro" class="size-4 text-amber-600 dark:text-amber-400" />
                                    </div>
                                    <div class="truncate font-medium text-zinc-900 dark:text-[#f0f0f2]">{{ $cost->name }}</div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell class="hidden lg:table-cell">
                                <div class="max-w-xs truncate text-zinc-600 dark:text-zinc-300">
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
                            <flux:table.cell variant="strong">
                                <span class="font-semibold text-zinc-900 dark:text-[#f0f0f2]">{{ number_format($total, 0, ',', ' ') }} {{ $project->currency }}</span>
                            </flux:table.cell>
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
        </div>

        @php
            $grandTotal = $project->externalCosts->sum(function ($cost) use ($months) {
                return match($cost->frequency->value) {
                    'one_time' => $cost->amount * $cost->quantity,
                    'monthly' => $cost->amount * $cost->quantity * $months,
                    'yearly' => $cost->amount * $cost->quantity * ceil($months / 12),
                };
            });
        @endphp

        <div class="flex justify-end">
            <div class="rounded-xl bg-teal-50 px-5 py-4 text-right ring-1 ring-teal-200 dark:bg-teal-500/10 dark:ring-teal-500/20 dark:shadow-[0_0_16px_-4px_rgba(45,212,191,0.15)]">
                <div class="text-xs font-semibold uppercase tracking-widest text-teal-600 dark:text-teal-400">Total coûts externes ({{ $months }} mois)</div>
                <div class="mt-1 text-2xl font-bold text-teal-600 dark:text-teal-400">{{ number_format($grandTotal, 0, ',', ' ') }} {{ $project->currency }}</div>
            </div>
        </div>
    @endif
</div>
