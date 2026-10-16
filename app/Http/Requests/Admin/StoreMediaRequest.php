<?php

namespace App\Http\Requests\Admin;

use App\Support\Rbac;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can(Rbac::PERMISSION_MEDIA_MANAGE) ?? false;
    }

    public function rules(): array
    {
        return [
            'files' => ['required', 'array', 'min:1', 'max:10'],
            'files.*' => Rule::forEach(function ($file): array {
                $isPdf = $file instanceof UploadedFile && $file->isValid() && $file->getMimeType() === 'application/pdf';

                return [
                    'bail', 'required', 'file',
                    'mimetypes:image/jpeg,image/png,image/webp,application/pdf',
                    'extensions:jpg,jpeg,png,webp,pdf',
                    'max:'.($isPdf ? '10240' : '5120'),
                    ...($isPdf ? [] : ['image']),
                ];
            }),
        ];
    }

    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->has('files')) {
                return;
            }

            foreach ($this->file('files', []) as $index => $file) {
                $attribute = 'files.'.$index;
                if ($validator->errors()->has($attribute)) {
                    continue;
                }
                $extensions = [
                    'image/jpeg' => ['jpg', 'jpeg'],
                    'image/png' => ['png'],
                    'image/webp' => ['webp'],
                    'application/pdf' => ['pdf'],
                ];
                if (! in_array(strtolower($file->getClientOriginalExtension()), $extensions[$file->getMimeType()] ?? [], true)) {
                    $validator->errors()->add($attribute, 'The file extension must match its content type.');
                }
                if (mb_strlen($file->getClientOriginalName()) > 255) {
                    $validator->errors()->add($attribute, 'The original filename must not exceed 255 characters.');
                }
                if (str_starts_with($file->getMimeType(), 'image/') && ! $file->dimensions()) {
                    $validator->errors()->add($attribute, 'The image dimensions could not be read.');
                }
            }
        }];
    }
}
