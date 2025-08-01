<?php

namespace App\Models;

use App\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Model;

class ProjectCategory extends Model
{
    use HasSlug;

    protected $fillable = [
        'client_id',
        'name',
        'slug',
    ];

    protected function sluggable(): string
    {
        return 'name';
    }
}
