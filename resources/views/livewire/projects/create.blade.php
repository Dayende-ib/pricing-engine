<div class="mx-auto max-w-3xl space-y-8">
    <div>
        <div class="flex items-center justify-between gap-4">
            <div>
                <flux:heading size="xl">Nouveau projet</flux:heading>
                <flux:subheading size="lg">Décrivez votre projet pour démarrer l'estimation</flux:subheading>
            </div>
        </div>

        <div class="mt-6 space-y-4">
            <flux:progress :value="$progress" color="indigo" />

            <div class="flex justify-between">
                @for($i = 1; $i <= $totalSteps; $i++)
                    <div class="relative flex flex-1 flex-col items-center">
                        @if($i < $totalSteps)
                            <div class="absolute top-4 left-1/2 z-0 h-0.5 w-full rounded-full bg-zinc-800/10 dark:bg-white/10"
                                 style="margin-left: -50%;"
                                 wire:ignore.self></div>
                        @endif

                        <div @class([
                            'relative z-10 flex size-8 items-center justify-center rounded-full border text-sm font-semibold transition-all duration-300',
                            'border-emerald-500 bg-emerald-500 text-white' => $i < $currentStep,
                            'border-indigo-500 bg-indigo-500 text-white ring-4 ring-indigo-500/15' => $i == $currentStep,
                            'border-zinc-800/15 bg-white text-zinc-400 dark:border-white/15 dark:bg-white/5 dark:text-zinc-500' => $i > $currentStep,
                        ])>
                            @if($i < $currentStep)
                                <flux:icon icon="check" variant="micro" class="size-4" />
                            @else
                                {{ $i }}
                            @endif
                        </div>

                        <span @class([
                            'mt-2 hidden text-xs sm:block',
                            'font-medium text-zinc-800 dark:text-white' => $i == $currentStep,
                            'text-zinc-400' => $i != $currentStep,
                        ])>
                            @switch($i)
                                @case(1) Informations @break
                                @case(2) Tarification @break
                                @case(3) Fonctionnalités @break
                                @case(4) Confirmation @break
                            @endswitch
                        </span>
                    </div>
                @endfor
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="space-y-6">
        @if($currentStep === 1)
            <flux:card variant="soft">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                        <flux:icon icon="folder" variant="outline" class="size-5 text-indigo-500" />
                    </div>
                    <flux:heading>Informations du projet</flux:heading>
                </div>

                <div class="mt-6 space-y-6">
                    <flux:field label="Nom du projet" required>
                        <flux:input wire:model.debounce.300ms="name" placeholder="Ex : Refonte site e-commerce" />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <flux:field label="Nom du client">
                            <flux:input icon="user" wire:model.debounce.300ms="client_name" placeholder="Ex : Entreprise ABC" />
                        </flux:field>

                        <flux:field label="Email du client">
                            <flux:input icon="envelope" type="email" wire:model.debounce.300ms="client_email" placeholder="contact@client.com" />
                        </flux:field>
                    </div>

                    <flux:field label="Type de projet" required>
                        <flux:select wire:model="project_type">
                            <flux:select.option value="web_app">Application Web</flux:select.option>
                            <flux:select.option value="mobile_app">Application Mobile</flux:select.option>
                            <flux:select.option value="api">API / Backend</flux:select.option>
                            <flux:select.option value="ecommerce">Site E-commerce</flux:select.option>
                            <flux:select.option value="dashboard">Dashboard / Admin</flux:select.option>
                            <flux:select.option value="landing_page">Landing Page</flux:select.option>
                            <flux:select.option value="other">Autre</flux:select.option>
                        </flux:select>
                    </flux:field>

                    <flux:field label="Description">
                        <flux:textarea wire:model.debounce.300ms="description" rows="4" resize="vertical"
                            placeholder="Décrivez le projet, ses objectifs, le contexte..." />
                    </flux:field>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                        <flux:field label="Date limite">
                            <flux:input type="date" wire:model="deadline" />
                        </flux:field>

                        <flux:field label="Budget min ({{ $currency }})">
                            <flux:input icon="banknotes" type="number" step="1000" min="0" wire:model="budget_min" placeholder="1 000 000" />
                        </flux:field>

                        <flux:field label="Budget max ({{ $currency }})">
                            <flux:input icon="banknotes" type="number" step="1000" min="0" wire:model="budget_max" placeholder="3 000 000" />
                        </flux:field>
                    </div>
                </div>
            </flux:card>
        @endif

        @if($currentStep === 2)
            <flux:card variant="soft">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                        <flux:icon icon="calculator" variant="outline" class="size-5 text-amber-500" />
                    </div>
                    <flux:heading>Profil de tarification</flux:heading>
                </div>

                <div class="mt-6 space-y-6">
                    @if($pricingProfiles->isEmpty())
                        <flux:callout icon="information-circle" color="amber" variant="soft">
                            <flux:callout.text>
                                Aucun profil tarifaire disponible. Créez d'abord un profil pour pouvoir tarifer vos projets.
                            </flux:callout.text>
                        </flux:callout>
                    @else
                        <flux:field label="Profil tarifaire" required>
                            <flux:radio.group variant="cards" wire:model="pricing_profile_id">
                                @foreach($pricingProfiles as $profile)
                                    <flux:radio value="{{ $profile->id }}" variant="cards" indicator="start">
                                        <div class="flex w-full items-center justify-between gap-4">
                                            <div class="flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <flux:heading>{{ $profile->name }}</flux:heading>
                                                    @if($profile->is_default)
                                                        <flux:badge color="indigo" rounded>Par défaut</flux:badge>
                                                    @endif
                                                </div>
                                                <flux:subheading size="sm">
                                                    {{ number_format($profile->hourly_rate, 0, ',', ' ') }} {{ $profile->currency }}/h
                                                </flux:subheading>
                                            </div>

                                            <div class="flex shrink-0 gap-2">
                                                <div class="rounded-lg bg-zinc-800/5 px-3 py-2 text-center dark:bg-white/10">
                                                    <div class="text-sm font-semibold text-zinc-800 dark:text-white">{{ $profile->minimum_margin * 100 }} %</div>
                                                    <div class="text-[11px] uppercase tracking-wide text-zinc-400">Marge min</div>
                                                </div>
                                                <div class="rounded-lg bg-indigo-500/10 px-3 py-2 text-center">
                                                    <div class="text-sm font-semibold text-indigo-500">{{ $profile->target_margin * 100 }} %</div>
                                                    <div class="text-[11px] uppercase tracking-wide text-zinc-400">Marge cible</div>
                                                </div>
                                                <div class="rounded-lg bg-zinc-800/5 px-3 py-2 text-center dark:bg-white/10">
                                                    <div class="text-sm font-semibold text-zinc-800 dark:text-white">{{ $profile->premium_margin * 100 }} %</div>
                                                    <div class="text-[11px] uppercase tracking-wide text-zinc-400">Marge premium</div>
                                                </div>
                                            </div>
                                        </div>
                                    </flux:radio>
                                @endforeach
                            </flux:radio.group>
                        </flux:field>

                        <flux:field label="Devise">
                            <flux:select wire:model="currency" class="max-w-xs">
                                <flux:select.option value="XOF">XOF (FCFA)</flux:select.option>
                                <flux:select.option value="EUR">EUR (€)</flux:select.option>
                                <flux:select.option value="USD">USD ($)</flux:select.option>
                            </flux:select>
                        </flux:field>
                    @endif
                </div>
            </flux:card>
        @endif

        @if($currentStep === 3)
            <flux:card variant="soft">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                        <flux:icon icon="squares-2x2" variant="outline" class="size-5 text-purple-500" />
                    </div>
                    <flux:heading>Fonctionnalités</flux:heading>
                </div>

                <div class="flex flex-col items-center px-6 py-12 text-center">
                    <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                        <flux:icon icon="squares-2x2" name="squares-2x2" variant="outline" class="size-8 text-zinc-400" />
                    </div>
                    <flux:heading class="mt-4">Gestion des fonctionnalités</flux:heading>
                    <flux:text class="mt-1 max-w-md">
                        Vous pourrez ajouter les fonctionnalités, coûts externes et ajustements après la création du projet, depuis la page de détail.
                    </flux:text>

                    <flux:button variant="outline" icon-trailing="arrow-right" wire:click="$set('currentStep', 4)" class="mt-6">
                        Passer cette étape
                    </flux:button>
                </div>
            </flux:card>
        @endif

        @if($currentStep === 4)
            @php
                $projectTypeLabels = [
                    'web_app' => 'Application Web',
                    'mobile_app' => 'Application Mobile',
                    'api' => 'API / Backend',
                    'ecommerce' => 'Site E-commerce',
                    'dashboard' => 'Dashboard / Admin',
                    'landing_page' => 'Landing Page',
                    'other' => 'Autre',
                ];
            @endphp

            <flux:card variant="soft">
                <div class="flex items-center gap-3">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-800/5 dark:bg-white/10">
                        <flux:icon icon="check-badge" variant="outline" class="size-5 text-emerald-500" />
                    </div>
                    <flux:heading>Confirmation</flux:heading>
                </div>

                <div class="mt-6 overflow-hidden rounded-xl border border-zinc-800/10 dark:border-white/10">
                    <dl class="divide-y divide-zinc-800/5 dark:divide-white/10">
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Nom du projet</dt>
                            <dd class="text-sm font-medium text-zinc-800 sm:col-span-2 dark:text-white">{{ $name }}</dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Client</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                                @if($client_name)
                                    {{ $client_name }}@if($client_email) ({{ $client_email }})@endif
                                @else
                                    <span class="text-zinc-400">Non spécifié</span>
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Type de projet</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                                {{ $projectTypeLabels[$project_type] ?? $project_type }}
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Profil tarifaire</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                                @if($pricingProfiles->firstWhere('id', $pricing_profile_id)?->name)
                                    {{ $pricingProfiles->firstWhere('id', $pricing_profile_id)?->name }}
                                @else
                                    <span class="text-zinc-400">Non défini</span>
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Devise</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">{{ $currency }}</dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Date limite</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                                @if($deadline)
                                    {{ \Carbon\Carbon::parse($deadline)->format('d/m/Y') }}
                                @else
                                    <span class="text-zinc-400">Non définie</span>
                                @endif
                            </dd>
                        </div>
                        <div class="grid grid-cols-1 gap-1 px-5 py-4 sm:grid-cols-3">
                            <dt class="text-sm font-medium text-zinc-400">Budget</dt>
                            <dd class="text-sm text-zinc-700 sm:col-span-2 dark:text-zinc-200">
                                @if($budget_min || $budget_max)
                                    {{ number_format($budget_min ?? 0, 0, ',', ' ') }} - {{ number_format($budget_max ?? 0, 0, ',', ' ') }} {{ $currency }}
                                @else
                                    <span class="text-zinc-400">Non défini</span>
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </flux:card>
        @endif

        <div class="flex items-center justify-between border-t border-zinc-800/10 pt-6 dark:border-white/10">
            @if($currentStep > 1)
                <flux:button variant="outline" icon="arrow-left" wire:click="previousStep">
                    Précédent
                </flux:button>
            @else
                <div></div>
            @endif

            @if($currentStep < $totalSteps)
                <flux:button variant="primary" icon-trailing="arrow-right" wire:click="nextStep">
                    Suivant
                </flux:button>
            @else
                <flux:button variant="primary" icon="check" type="submit">
                    Créer le projet
                </flux:button>
            @endif
        </div>
    </form>
</div>