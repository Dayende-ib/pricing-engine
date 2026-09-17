<?php

namespace App\Enums;

enum QuoteStatus: string
{
    case Draft = 'draft';
    case Sent = 'sent';
    case Viewed = 'viewed';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Expired = 'expired';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Sent => 'Envoyé',
            self::Viewed => 'Consulté',
            self::Accepted => 'Accepté',
            self::Rejected => 'Refusé',
            self::Expired => 'Expiré',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Sent => 'blue',
            self::Viewed => 'indigo',
            self::Accepted => 'green',
            self::Rejected => 'red',
            self::Expired => 'gray',
        };
    }
}
