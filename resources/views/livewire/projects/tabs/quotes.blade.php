<div class="space-y-6">
    <div>
        <flux:heading>Devis générés</flux:heading>
        <flux:text size="sm" class="mt-0.5 text-zinc-500 dark:text-zinc-400">Historique des devis et leur statut</flux:text>
    </div>

    @if($project->quotes->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                <flux:icon icon="document-text" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            </div>
            <flux:heading class="mt-4">Aucun devis</flux:heading>
            <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">
                Une fois le prix calculé, vous pourrez générer un devis directement depuis cette page.
            </flux:text>
        </div>
    @else
        @foreach($project->quotes as $quote)
            <div class="overflow-hidden rounded-2xl border border-zinc-200/60 bg-white dark:border-white/[0.06] dark:bg-surface">
                <div class="flex flex-col gap-4 border-b border-zinc-100 p-5 sm:flex-row sm:items-start sm:justify-between dark:border-white/[0.06] sm:p-6">
                    <div>
                        <div class="flex flex-wrap items-center gap-3">
                            <flux:heading>{{ $quote->quote_number }}</flux:heading>
                            <x-status-badge :status="$quote->status->value">{{ $quote->status->label() }}</x-status-badge>
                        </div>
                        <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">
                            Créé le {{ $quote->created_at->format('d/m/Y') }} · Valide jusqu'au {{ $quote->valid_until?->format('d/m/Y') ?? 'Non défini' }}
                        </flux:text>
                    </div>

                    <div class="flex items-center gap-4">
                        <div class="text-right">
                            <div class="text-2xl font-bold text-zinc-900 dark:text-[#f0f0f2]">
                                {{ number_format($quote->total, 0, ',', ' ') }} <span class="text-sm font-normal text-zinc-400">{{ $quote->currency }}</span>
                            </div>
                            <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">{{ $quote->items->count() }} ligne(s)</flux:text>
                        </div>

                        <div class="flex gap-2">
                            @if(Route::has('quotes.show'))
                                <flux:button variant="outline" icon="eye" size="sm" href="{{ route('quotes.show', $quote) }}" wire:navigate>
                                    Voir
                                </flux:button>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <flux:table class="text-sm">
                        <flux:table.columns>
                            <flux:table.column>Description</flux:table.column>
                            <flux:table.column align="center">Qté</flux:table.column>
                            <flux:table.column>Prix unitaire</flux:table.column>
                            <flux:table.column align="end">Total</flux:table.column>
                        </flux:table.columns>

                        <flux:table.rows>
                            @foreach($quote->items as $item)
                                <flux:table.row>
                                    <flux:table.cell variant="strong">{{ $item->description }}</flux:table.cell>
                                    <flux:table.cell align="center">{{ number_format($item->quantity, 0) }}</flux:table.cell>
                                    <flux:table.cell>{{ number_format($item->unit_price, 0, ',', ' ') }} {{ $quote->currency }}</flux:table.cell>
                                    <flux:table.cell align="end" variant="strong">{{ number_format($item->total, 0, ',', ' ') }} {{ $quote->currency }}</flux:table.cell>
                                </flux:table.row>
                            @endforeach
                        </flux:table.rows>
                    </flux:table>
                </div>
            </div>
        @endforeach
    @endif
</div>
