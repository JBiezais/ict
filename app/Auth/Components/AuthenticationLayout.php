<?php

namespace App\Auth\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class AuthenticationLayout extends Component
{
    public function render(): View
    {
        return view('auth.layout.authentication');
    }
}
