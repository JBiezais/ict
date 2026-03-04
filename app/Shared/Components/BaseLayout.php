<?php

namespace App\Shared\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class BaseLayout extends Component
{
    public function render(): View
    {
        return view('layouts.base');
    }
}
