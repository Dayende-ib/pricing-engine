<div>
    <div>
        <flux:heading>{{ $editingExternalCost ? 'Modifier le coût externe' : 'Nouveau coût externe' }}</flux:heading>
        <flux:text size="sm">Licences, hébergement, services tiers...</flux:text>
    </div>

    <form wire:submit.prevent="saveExternalCost" class="mt-6 space-y-5">
        <flux:field label="Nom" required>
            <flux:input wire:model="external_cost_name" placeholder="Ex : Hébergement serveur" />
        </flux:field>

        <flux:field label="Description">
            <flux:textarea wire:model="external_cost_description" rows="3" resize="vertical" placeholder="Description détaillée..." />
        </flux:field>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <flux:field label="Montant" required>
                <flux:input icon="banknotes" type="number" step="1000" min="0" wire:model="external_cost_amount" placeholder="Ex : 35 000" />
            </flux:field>

            <flux:field label="Quantité" required>
                <flux:input type="number" min="1" wire:model="external_cost_quantity" />
            </flux:field>
        </div>

        <flux:field label="Fréquence" required>
            <flux:select wire:model="external_cost_frequency">
                <flux:select.option value="one_time">Ponctuel (une seule fois)</flux:select.option>
                <flux:select.option value="monthly">Mensuel</flux:select.option>
                <flux:select.option value="yearly">Annuel</flux:select.option>
            </flux:select>
        </flux:field>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-800/5 pt-5 dark:border-white/10">
            <flux:modal.close>
                <flux:button variant="outline" wire:click="closeModals">Annuler</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" icon="check" type="submit" wire:loading.attr="disabled">
                {{ $editingExternalCost ? 'Mettre à jour' : 'Ajouter' }}
            </flux:button>
        </div>
    </form>
</div>