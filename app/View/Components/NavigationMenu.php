<?php

namespace App\View\Components;

use App\Support\MenuResolver;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NavigationMenu extends Component
{
    public array $nodes;

    public function __construct(MenuResolver $resolver, public ?string $location = null, ?string $slug = null, public string $label = 'Website navigation')
    {
        $this->nodes = $slug !== null ? $resolver->forSlug($slug) : $resolver->forLocation($location ?? '');
    }

    public function render(): View
    {
        return view('components.navigation-menu');
    }
}
