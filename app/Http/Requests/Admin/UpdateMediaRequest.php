<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;

class UpdateMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Rbac::PERMISSION_MEDIA_MANAGE) ?? false;
    }

    public function rules(): array
    {
        return ['title' => ['present', 'nullable', 'string', 'max:255']];
    }
}
