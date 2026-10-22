<?php

namespace App\Http\Requests\Admin;

use App\Enums\PageSectionType;
use App\Models\PageSection;
use App\Rules\SafeSectionUrl;
use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StorePageSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Rbac::PERMISSION_PAGES_MANAGE) ?? false;
    }

    protected function updating(): bool
    {
        return false;
    }

    public function sectionType(): ?PageSectionType
    {
        return $this->updating() ? $this->route('section')->type : PageSectionType::tryFrom(is_string($this->input('type')) ? $this->input('type') : '');
    }

    protected function prepareForValidation(): void
    {
        foreach (['heading', 'body', 'image_alt', 'button_label', 'button_url'] as $field) {
            if (is_string($this->input($field))) {
                $value = trim($this->input($field));
                $this->merge([$field => $value === '' ? null : $value]);
            }
        }
        if (! $this->updating() && ! $this->exists('is_enabled')) {
            $this->merge(['is_enabled' => true]);
        }
    }

    public function rules(): array
    {
        $type = $this->sectionType();
        $text = fn (int $max, bool $required = false): array => [$required ? 'required' : 'nullable', 'string', 'max:'.$max, 'not_regex:/[<>]/'];
        $image = $type === PageSectionType::ImageText;
        $hero = $type === PageSectionType::Hero;

        return [
            'type' => $this->updating() ? ['missing'] : ['required', Rule::enum(PageSectionType::class)],
            'heading' => $text(200, true),
            'body' => $text(match ($type) {
                PageSectionType::Text => 10000, PageSectionType::ImageText => 5000, default => 500
            }, ! $hero),
            'is_enabled' => ['required', 'boolean'],
            'media_id' => $type === PageSectionType::Text ? ['missing'] : [$image ? 'required' : 'nullable', 'integer', Rule::exists('media', 'id')->whereIn('mime_type', PageSection::IMAGE_MIMES)],
            'image_alt' => $type === PageSectionType::Text ? ['missing'] : [...$text(255), Rule::requiredIf(fn () => $this->filled('media_id')), Rule::prohibitedIf(fn () => ! $this->filled('media_id'))],
            'button_label' => $hero ? [...$text(80), 'required_with:button_url'] : ['missing'],
            'button_url' => $hero ? ['nullable', 'string', 'max:2048', 'required_with:button_label', new SafeSectionUrl] : ['missing'],
            'page_id' => ['missing'], 'id' => ['missing'], 'sort_order' => ['missing'],
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            $allowed = ['type', 'heading', 'body', 'is_enabled', 'media_id', 'image_alt', 'button_label', 'button_url', '_token', '_method'];
            foreach (array_diff(array_keys($this->all()), $allowed) as $field) {
                $validator->errors()->add($field, 'This field is not editable.');
            }
        }];
    }
}
