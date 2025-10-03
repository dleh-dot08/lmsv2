<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class DashboardCard extends Component
{
    // PASTI ADA DEKLARASI PUBLIC STRING INI!
    public string $icon;
    public string $title;
    public string $value;
    public string $color;

    public function __construct(string $icon, string $title, string $value, string $color)
    {
        $this->icon = $icon;
        $this->title = $title;
        $this->value = $value;
        $this->color = $color;
    }

    public function render(): View
    {
        return view('components.dashboard-card');
    }
}