<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use MongoDB\Laravel\Eloquent\Model as MongoModel;
use Illuminate\Notifications\Notifiable;
use MongoDB\BSON\ObjectId;

class User extends MongoModel
{
    use Notifiable;

    /**
     * The connection name for the model.
     */
    protected $connection = 'mongodb_cmym';

    /**
     * The collection associated with the model.
     */
    protected $collection = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'empresa_id',
        'estado',
        '_esBorrado',
        'email_verified_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
            'estado' => 'boolean',
            '_esBorrado' => 'boolean',
        ];
    }

    public function empresa()
    {
        return $this->belongsTo(\App\Models\Empresa::class, 'empresa_id');
    }

    /**
     * Scope a query to only include active users.
     */
    public function scopeActivo($query)
    {
        return $query->where('estado', true)->where('_esBorrado', false);
    }
}
