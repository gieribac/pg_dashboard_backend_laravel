<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasFactory;
    protected $table = 'maps';//nombre del modelo
    protected $fillable = [ //campos alterables
        'name',
        'description',
        'author',
        'date',
        'place'
    ];
}
