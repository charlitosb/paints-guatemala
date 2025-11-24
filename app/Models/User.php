<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'tienda_id',
        'activo',
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
            'activo' => 'boolean',
        ];
    }

    /**
     * Relación: Un usuario pertenece a una tienda
     */
    public function tienda()
    {
        return $this->belongsTo(Tienda::class);
    }

    /**
     * Relación: Un usuario puede tener muchas facturas
     */
    public function facturas()
    {
        return $this->hasMany(Factura::class);
    }

    /**
     * Relación: Un usuario puede tener muchos ingresos de productos
     */
    public function ingresosProductos()
    {
        return $this->hasMany(IngresoProducto::class);
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    /**
     * Verificar si el usuario es digitador
     */
    public function isDigitador()
    {
        return $this->role === 'digitador';
    }

    /**
     * Verificar si el usuario es cajero
     */
    public function isCajero()
    {
        return $this->role === 'cajero';
    }

    /**
     * Verificar si el usuario es gerente
     */
    public function isGerente()
    {
        return $this->role === 'gerente';
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Verificar si el usuario tiene alguno de los roles especificados
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, (array) $roles);
    }
}