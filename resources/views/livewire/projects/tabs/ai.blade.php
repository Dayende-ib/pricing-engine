<div class="space-y-6">
    <div>
        <flux:heading>Analyses IA</flux:heading>
        <flux:subheading size="sm">Estimations générées par intelligence artificielle</flux:subheading>
    </div>

    @if($project->aiAnalyses->isEmpty())
        <div class="flex flex-col items-center px-6 py-16 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-800/5 dark:bg-white/10">
                <flux:icon icon="sparkles" variant="outline" class="size-8 text-zinc-400" />
            </div>
            <flux:heading class="mt-4">Aucune analyse IA</flux:heading>
            <flux:text class="mt-1 max-w-md">L'analyse IA sera disponible prochainement.</flux:text>
        </div>
    @else
        @foreach($project->aiAnalyses as $analysis)
            <flux:card variant="soft">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <flux:heading>Analyse du {{ $analysis->created_at->format('d/m/Y à H:i') }}</flux:heading>
                        <flux:text size="sm" class="mt-1">{{ $analysis->provider ?? 'OpenRouter' }} • {{ $analysis->model ?? 'N/A' }}</flux:text>
                    </div>

                    <div class="flex items-center gap-3">
                        <x-status-badge :status="$analysis->status->value">
                            {{ $analysis->status->label() }}
                        </x-status-badge>
                        @if($analysis->confidence)
                            <flux:badge color="zinc" rounded icon="percent">
                                Confiance {{ number_format($analysis->confidence * 100, 0) }} %
                            </flux:badge>
                        @endif
                    </div>
                </div>

                @if($analysis->output)
                    <div class="mt-4 overflow-x-auto rounded-xl bg-zinc-800/5 p-4 dark:bg-white/10">
                        <pre class="text-sm whitespace-pre-wrap text-zinc-700 dark:text-zinc-200">{{ $analysis->output }}</pre>
                    </div>
                @endif
            </flux:card>
        @endforeach
    @endif
</div>