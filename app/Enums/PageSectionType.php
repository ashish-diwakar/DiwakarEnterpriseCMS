<?php

namespace App\Enums;

enum PageSectionType: string
{
    case Hero = 'hero';
    case Text = 'text';
    case ImageText = 'image_text';

    public function label(): string
    {
        return match ($this) {
            self::Hero => 'Hero', self::Text => 'Text Content', self::ImageText => 'Image with Text',
        };
    }
}
