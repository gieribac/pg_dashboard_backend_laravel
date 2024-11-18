<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;
    protected $table = 'admin';//nombre del modelo
    protected $fillable = [ //campos editables
        'name',
        'no_doc',
        'email',
        'username',
        'password'
    ];
}
