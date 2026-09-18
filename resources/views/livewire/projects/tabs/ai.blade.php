<div class="space-y-6">
    <div>
        <flux:heading>Analyses IA</flux:heading>
        <flux:text size="sm" class="mt-0.5 text-zinc-500 dark:text-zinc-400">Estimations générées par intelligence artificielle</flux:text>
    </div>

    @if($project->aiAnalyses->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-white/[0.04]">
                <flux:icon icon="sparkles" variant="outline" class="size-8 text-zinc-300 dark:text-zinc-600" />
            </div>
            <flux:heading class="mt-4">Aucune analyse IA</flux:heading>
            <flux:text class="mt-1 max-w-md text-zinc-500 dark:text-zinc-400">L'analyse IA sera disponible prochainement.</flux:text>
        </div>
    @else
        @foreach($project->aiAnalyses as $analysis)
            <div class="overflow-hidden rounded-2xl border border-zinc-200/60 bg-white dark:border-white/[0.06] dark:bg-surface">
                <div class="flex flex-col gap-4 border-b border-zinc-100 p-5 sm:flex-row sm:items-start sm:justify-between dark:border-white/[0.06] sm:p-6">
                    <div>
                        <flux:heading>Analyse du {{ $analysis->created_at->format('d/m/Y à H:i') }}</flux:heading>
                        <flux:text size="sm" class="mt-1 text-zinc-500 dark:text-zinc-400">{{ $analysis->provider ?? 'OpenRouter' }} · {{ $analysis->model ?? 'N/A' }}</flux:text>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$analysis->status->value">
                            {{ $analysis->status->label() }}
                        </x-status-badge>
                        @if($analysis->confidence)
                            <flux:badge color="teal" rounded icon="percent">
                                Confiance {{ number_format($analysis->confidence * 100, 0) }}%
                            </flux:badge>
                        @endif
                    </div>
                </div>

                @if($analysis->output)
                    <div class="p-6">
                        <div class="overflow-x-auto rounded-xl bg-zinc-50 p-4 dark:bg-background">
                            <pre class="text-sm whitespace-pre-wrap text-zinc-700 dark:text-zinc-200">{{ $analysis->output }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
    @endif
</div>
