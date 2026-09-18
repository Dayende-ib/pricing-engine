<div>
    <div>
        <flux:heading>{{ $editingFeature ? 'Modifier la fonctionnalité' : 'Nouvelle fonctionnalité' }}</flux:heading>
        <flux:text size="sm" class="text-zinc-500 dark:text-zinc-400">Estimez les heures nécessaires pour cette fonctionnalité.</flux:text>
    </div>

    <form wire:submit.prevent="saveFeature" class="mt-6 space-y-5">
        <flux:field label="Nom" required>
            <flux:input wire:model="feature_name" placeholder="Ex : Authentification utilisateur" />
        </flux:field>

        <flux:field label="Description">
            <flux:textarea wire:model="feature_description" rows="3" resize="vertical" placeholder="Description détaillée..." />
        </flux:field>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <flux:field label="Quantité" required>
                <flux:input type="number" min="1" wire:model="feature_quantity" />
            </flux:field>

            <flux:field label="Heures estimées" required>
                <flux:input type="number" step="0.5" min="0" wire:model="feature_estimated_hours" placeholder="Ex : 8.5" />
            </flux:field>
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <flux:field label="Complexité" required>
                <flux:select wire:model="feature_complexity">
                    <flux:select.option value="low">Faible</flux:select.option>
                    <flux:select.option value="normal">Normale</flux:select.option>
                    <flux:select.option value="high">Élevée</flux:select.option>
                    <flux:select.option value="very_high">Très élevée</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field label="Priorité" required>
                <flux:select wire:model="feature_priority">
                    <flux:select.option value="low">Faible</flux:select.option>
                    <flux:select.option value="normal">Normale</flux:select.option>
                    <flux:select.option value="high">Haute</flux:select.option>
                    <flux:select.option value="critical">Critique</flux:select.option>
                </flux:select>
            </flux:field>
        </div>

        <flux:field label="Source d'estimation" required>
            <flux:select wire:model="feature_estimation_source">
                <flux:select.option value="human">Manuel</flux:select.option>
                <flux:select.option value="ai">IA</flux:select.option>
                <flux:select.option value="template">Modèle</flux:select.option>
                <flux:select.option value="historical">Historique</flux:select.option>
            </flux:select>
        </flux:field>

        <div class="mt-6 flex items-center justify-end gap-3 border-t border-zinc-200/60 pt-5 dark:border-white/[0.06]">
            <flux:modal.close>
                <flux:button variant="outline" wire:click="closeModals">Annuler</flux:button>
            </flux:modal.close>

            <flux:button variant="primary" icon="check" type="submit" wire:loading.attr="disabled">
                {{ $editingFeature ? 'Mettre à jour' : 'Ajouter' }}
            </flux:button>
        </div>
    </form>
</div>
