<?php

namespace App\Enums;

enum MenuLocation: string
{
    case Header = 'header';
    case Footer = 'footer';

    public function label(): string
    {
        return match ($this) {
            self::Header => 'Header / Main Navigation',
            self::Footer => 'Footer Navigation',
        };
    }
}
