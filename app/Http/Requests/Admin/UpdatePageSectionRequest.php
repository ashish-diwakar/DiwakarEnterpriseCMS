<?php

namespace App\Http\Requests\Admin;

class UpdatePageSectionRequest extends StorePageSectionRequest
{
    protected function updating(): bool
    {
        return true;
    }
}
