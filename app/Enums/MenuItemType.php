<?php

namespace App\Enums;

enum MenuItemType: string
{
    case Page = 'page';
    case Url = 'url';
    case Group = 'group';

    public function label(): string
    {
        return match ($this) {
            self::Page => 'CMS Page', self::Url => 'Custom URL', self::Group => 'Navigation group',
        };
    }
}
