<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MutateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('menus.update') ?? false;
    }

    public function rules(): array
    {
        $rules = ['version' => ['required', 'integer', 'min:0']];
        if ($this->routeIs('admin.menus.items.move')) {
            $rules['direction'] = ['required', Rule::in(['up', 'down'])];
        }
        if ($this->routeIs('admin.menus.items.reorder')) {
            $rules['parent_id'] = ['nullable', 'integer'];
            $rules['items'] = ['present', 'array'];
            $rules['items.*'] = ['required', 'integer', 'min:1', 'distinct'];
        }

        return $rules;
    }
}
