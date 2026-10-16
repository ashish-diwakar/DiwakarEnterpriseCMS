<?php

namespace App\Http\Requests\Admin;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Rbac::PERMISSION_PAGES_MANAGE) ?? false;
    }

    protected function updating(): bool
    {
        return false;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('title'))) {
            $this->merge(['title' => trim($this->input('title'))]);
        }

        $slug = $this->input('slug');
        if (! $this->updating() && ($slug === null || (is_string($slug) && trim($slug) === ''))) {
            if (is_string($this->input('title'))) {
                $this->merge(['slug' => Str::slug($this->input('title'))]);
            }
        } elseif (is_string($slug) && preg_match('/\A[\pL\pM\pN _-]*\z/u', trim($slug))) {
            $this->merge(['slug' => Str::slug(trim($slug))]);
        }

        if (! $this->updating() && ! $this->exists('status')) {
            $this->merge(['status' => PageStatus::Draft->value]);
        }
    }

    public function rules(): array
    {
        $unique = Rule::unique('pages', 'slug');
        if ($this->updating()) {
            $unique->ignore($this->route('page'));
        }
        $presence = $this->updating() ? ['sometimes', 'required'] : ['required'];

        return [
            'title' => [...$presence, 'bail', 'string', 'max:200', 'not_regex:/[<>]/'],
            'slug' => [...$presence, 'bail', 'string', 'max:150', 'regex:/\A[a-z0-9]+(?:-[a-z0-9]+)*\z/', Rule::notIn(Page::RESERVED_SLUGS), $unique],
            'status' => [...$presence, Rule::enum(PageStatus::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'title.not_regex' => 'The title must be plain text without angle brackets.',
            'slug.regex' => 'Use a single slug with letters, numbers and hyphens; paths and URLs are not allowed.',
            'slug.not_in' => 'This slug is reserved for the application.',
        ];
    }
}
