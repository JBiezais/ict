<?php

namespace App\Post\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class PostFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $replace = [];
        if ($this->has('date_from') && $this->date_from === '') {
            $replace['date_from'] = null;
        }
        if ($this->has('date_to') && $this->date_to === '') {
            $replace['date_to'] = null;
        }
        $search = $this->input('search');
        if ($this->has('search') && is_string($search)) {
            $replaced = preg_replace('/\s+/', ' ', $search);
            $replace['search'] = trim(is_string($replaced) ? $replaced : $search);
        }
        if ($replace !== []) {
            $this->merge($replace);
        }
    }
}
