<?php

namespace App\Http\Requests\Admin;

class UpdatePageRequest extends StorePageRequest
{
    protected function updating(): bool
    {
        return true;
    }
}
