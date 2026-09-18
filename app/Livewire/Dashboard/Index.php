<?php

namespace App\Livewire\Dashboard;

use App\Models\Project;
use App\Models\Quote;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Index extends Component
{
    public int $projectsThisMonth = 0;

    public int $quotesGenerated = 0;

    public float $potentialRevenue = 0;

    public float $acceptedRevenue = 0;

    public float $averageProjectPrice = 0;

    public float $averageMargin = 0;

    public function mount(): void
    {
        $this->loadStats();
        $this->loadRecentProjects();
    }

    public function loadStats(): void
    {
        $user = auth()->user();
        $startOfMonth = now()->startOfMonth();

        $this->projectsThisMonth = Project::where('user_id', $user->id)
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        $this->quotesGenerated = Quote::whereHas('project', fn ($q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', $startOfMonth)
            ->count();

        $quotes = Quote::whereHas('project', fn ($q) => $q->where('user_id', $user->id))
            ->where('created_at', '>=', $startOfMonth)
            ->get();

        $this->potentialRevenue = $quotes->sum('total');

        $acceptedQuotes = $quotes->where('status', 'accepted');
        $this->acceptedRevenue = $acceptedQuotes->sum('total');

        if ($this->quotesGenerated > 0) {
            $this->averageProjectPrice = $quotes->avg('total');
        }

        if ($acceptedQuotes->count() > 0) {
            $this->averageMargin = $acceptedQuotes->avg(fn ($q) => $q->project->pricingProfile->target_margin * 100);
        }
    }

    public function loadRecentProjects(): void
    {
        $this->recentProjects = Project::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->with('quotes')
            ->get();
    }

    /** @var Collection */
    public $recentProjects;

    public function render()
    {
        return view('livewire.dashboard.index');
    }
}
