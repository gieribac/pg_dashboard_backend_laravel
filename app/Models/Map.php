<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;



class Map extends Model
{
    use HasFactory;
    protected $table = 'map';//nombre del modelo
    protected $fillable = [ //campos alterables
        'post',
        'title',
        'description',
        'author',
        'urlDashboard',
        'place'
    ];
    protected function post(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool) $value,
        );
    }
}
