<?php

namespace App\Http\Requests\Admin;

use App\Enums\MenuItemType;
use App\Rules\SafeMenuUrl;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('menus.update') ?? false;
    }

    public function rules(): array
    {
        return [
            'version' => ['required', 'integer', 'min:0'],
            'label' => ['required', 'string', 'max:200'],
            'type' => ['required', Rule::enum(MenuItemType::class)],
            'parent_id' => ['nullable', 'integer', Rule::exists('menu_items', 'id')->where('menu_id', $this->route('menu')->id)],
            'page_id' => ['exclude_unless:type,page', 'required', 'integer', Rule::exists('pages', 'id')],
            'url' => ['exclude_unless:type,url', 'required', 'string', 'max:2048', new SafeMenuUrl],
            'target' => ['required', Rule::in(['_self', '_blank'])],
            'is_active' => ['required', 'boolean'],
        ];
    }
}
