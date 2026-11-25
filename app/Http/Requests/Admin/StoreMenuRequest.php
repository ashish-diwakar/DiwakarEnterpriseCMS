<?php

namespace App\Http\Requests\Admin;

use App\Enums\MenuLocation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreMenuRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can($this->route('menu') ? 'menus.update' : 'menus.create') ?? false;
    }

    protected function prepareForValidation(): void
    {
        if (! $this->route('menu') && ! $this->filled('slug') && is_string($this->input('name'))) {
            $this->merge(['slug' => Str::slug($this->input('name'))]);
        }
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:200'],
            'slug' => ['required', 'string', 'max:150', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', Rule::unique('menus')->ignore($this->route('menu'))],
            'description' => ['nullable', 'string', 'max:2000'],
            'location' => ['nullable', Rule::enum(MenuLocation::class), Rule::unique('menus')->ignore($this->route('menu'))],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
