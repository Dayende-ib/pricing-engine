<?php

use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Projects\Create as ProjectsCreate;
use App\Livewire\Projects\Index as ProjectsIndex;
use App\Livewire\Projects\Show as ProjectsShow;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

    Route::prefix('projects')->name('projects.')->group(function () {
        Route::get('/', ProjectsIndex::class)->name('index');
        Route::get('/create', ProjectsCreate::class)->name('create');
        Route::get('/{project}', ProjectsShow::class)->name('show');
        Route::get('/{project}/edit', ProjectsCreate::class)->name('edit');
    });
});

require __DIR__.'/settings.php';
