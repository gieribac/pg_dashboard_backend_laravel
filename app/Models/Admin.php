<?php

namespace App\Models;


use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    protected $table = 'admin';//nombre del modelo
    protected $fillable = [ //campos editables
        'name',
        'no_doc',
        'email',
        'username',
        'password',
        'main'
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Devolver un array con cualquier información personalizada que quieras agregar al token.
     */
    public function getJWTCustomClaims()
    {
        return [
            'main'=>$this->main,
            'name'=>$this->name,
            'no_doc'=>$this->no_doc,
            'email'=>$this->email,
            'username'=>$this->username
        ];
    }
}
