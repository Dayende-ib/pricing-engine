<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Draft = 'draft';
    case Analyzing = 'analyzing';
    case Review = 'review';
    case Priced = 'priced';
    case Quoted = 'quoted';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Analyzing => 'Analyse en cours',
            self::Review => 'En révision',
            self::Priced => 'Tarifié',
            self::Quoted => 'Devis généré',
            self::Accepted => 'Accepté',
            self::Rejected => 'Refusé',
            self::InProgress => 'En cours',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Analyzing => 'blue',
            self::Review => 'yellow',
            self::Priced => 'purple',
            self::Quoted => 'indigo',
            self::Accepted => 'green',
            self::Rejected => 'red',
            self::InProgress => 'blue',
            self::Completed => 'emerald',
            self::Cancelled => 'gray',
        };
    }
}
