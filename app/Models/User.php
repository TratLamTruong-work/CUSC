<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';

    // [BỔ SUNG] KHÔNG có 'role' trong $fillable: đăng ký không thể tự đặt role.
    // Cột role của người đăng ký mới lấy mặc định 'user' từ database (database/sql/schema.sql).
    protected $fillable = ['username', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function roleLabel(): string
    {
        return $this->isAdmin() ? 'Admin' : 'User';
    }
}
