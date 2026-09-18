<?php

namespace App\Livewire;

use Livewire\Component;

class ThemeToggle extends Component
{
    public bool $isDark = true;

    public function mount(): void
    {
        $theme = session('theme') ?? request()->cookie('theme');
        if ($theme === null) {
            $this->isDark = true;
        } else {
            $this->isDark = $theme === 'dark';
        }

        $this->dispatch('theme-changed', dark: $this->isDark);
    }

    public function toggle(): void
    {
        $this->isDark = ! $this->isDark;
        session(['theme' => $this->isDark ? 'dark' : 'light']);

        $this->dispatch('theme-changed', dark: $this->isDark);
    }

    public function render()
    {
        return view('livewire.theme-toggle');
    }
}
