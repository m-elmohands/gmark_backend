<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Availability: string implements HasLabel
{
    case Available = 'available';
    case Reserved = 'reserved';
    case Sold = 'sold';
    case ComingSoon = 'coming_soon';
    case OnRequest = 'on_request';

    public function getLabel(): string
    {
        return match ($this) {
            self::Available => __('Available'),
            self::Reserved => __('Reserved'),
            self::Sold => __('Sold'),
            self::ComingSoon => __('Coming soon'),
            self::OnRequest => __('On request'),
        };
    }
}
