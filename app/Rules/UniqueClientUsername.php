<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class UniqueClientUsername implements ValidationRule
{
    protected $parentId;

    protected $ignoreId;

    public function __construct($parentId, $ignoreId = null)
    {
        $this->parentId = $parentId;
        $this->ignoreId = $ignoreId;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $query = DB::table('clients')
            ->where('username', $value)
            ->where(function ($q) {
                $q->where('parent_id', $this->parentId)
                    ->orWhere('id', $this->parentId); // include the parent client itself
            });

        if ($this->ignoreId) {
            $query->where('id', '!=', $this->ignoreId);
        }

        if ($query->exists()) {
            $fail('The :attribute has already been taken for this client.');
        }
    }
}
