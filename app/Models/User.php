<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'role', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'administrador';

    public const ROLE_VENDEDOR = 'vendedor';

    public const ROLE_DEPOSITO = 'deposito';

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

    /**
     * Ruta de inicio según el rol (criterio de HU1).
     */
    public function homePath(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => '/',
            self::ROLE_VENDEDOR => '/ventas',
            self::ROLE_DEPOSITO => '/stock',
            default => '/',
        };
    }

    /**
     * Nombre visible del rol para la interfaz.
     */
    public function roleLabel(): string
    {
        return match ($this->role) {
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_VENDEDOR => 'Vendedor',
            self::ROLE_DEPOSITO => 'Depósito',
            default => 'Usuario',
        };
    }
}
