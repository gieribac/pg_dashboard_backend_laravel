<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Authorization extends Model
{
    protected $table = 'authorization';//nombre del modelo
    protected $fillable = [ //campos alterables
        'no_doc',
        'email',
    ];
}

