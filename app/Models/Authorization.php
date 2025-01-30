<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    protected $table = 'authorization';//nombre del modelo
    protected $fillable = [ //campos alterables
        'no_doc',
        'email',
        'main'
    ];
    protected function main(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }
}

