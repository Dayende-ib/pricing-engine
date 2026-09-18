<div>
    <div>
        <flux:heading>{{ $editingAdjustment ? "Modifier l'ajustement" : 'Nouvel ajustement' }}</flux:heading>
        <flux:text size="sm">Ajustez le prix selon l'urgence, le contexte, la négociation...</flux:text>
    </div>

    <form wire:submit.prevent="saveAdjustment" class="mt-6 space-y-5">
        <flux:field label="Nom" required>
            <flux:input wire:model="adjustment_name" placeholder="Ex : Urgence, complexité technique..." />
        </flux:field>

        <flux:field label="Description">
            <flux:textarea wire:model="adjustment_description" rows="3" resize="vertical" placeholder="Raison de l'ajustement..." />
        </flux:field>

        <flux:field label="Type" required>
            <flux:select wire:model="adjustment_type">
                <flux:select.option value="percentage">Pourcentage (%)</flux:select.option>
                <flux:select.option value="fixed">Montant fixe</flux:select.option>
            </flux:select>
        </flux:field>

        <flux:field
            :label="$adjustment_type === 'percentage' ? 'Valeur (%)' : 'Valeur (' . $project->currency . ')'"
            :description="$adjustment_type === 'percentage' ? 'Ex : 15 pour 15 %' : 'Montant en ' . $project->currency"
            required
        >
            <flux:input
                :icon="$adjustment_type === 'percentage' ? 'percent' : 'banknotes'"
                type="number"
                :step="$adjustment_type === 'percentage' ? '0.1' : '1000'"
                min="0"
                wire:model="adjustment_value"
                :placeholder="$adjustment_type === 'percentage' ? '15' : '50 000'"
            />
        </flux:field>

        <flux:field label="Justification">
            <flux:textarea wire:model="adjustment_reason" rows="2" resize="vertical" placeholder="Ex : Délai très court demandé par le client" />
        </flux:field>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-800/5 pt-5 dark:border-white/10">
            <flux:modal.close>
                <flux:button variant="outline" wire:click="closeModals">Annuler</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" icon="check" type="submit" wire:loading.attr="disabled">
                {{ $editingAdjustment ? 'Mettre à jour' : 'Ajouter' }}
            </flux:button>
        </div>
    </form>
</div>