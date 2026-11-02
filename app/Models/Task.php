<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Task extends Model
{
    public const PRIORITIES = [
        'low' => 'Thấp',
        'medium' => 'Trung bình',
        'high' => 'Cao',
    ];

    public const STATUSES = [
        'pending' => 'Chưa làm',
        'in_progress' => 'Đang làm',
        'completed' => 'Hoàn thành',
    ];

    // Không có user_id: luôn lấy từ người đăng nhập qua $user->tasks()->create().
    protected $fillable = ['title', 'description', 'start_date', 'due_date', 'priority', 'status'];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // [BỔ SUNG] Quá hạn: hạn chót trước hôm nay và chưa hoàn thành (tính động, không lưu DB).
    public function isOverdue(): bool
    {
        return $this->status !== 'completed' && $this->due_date->lt(Carbon::today());
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function priorityLabel(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'in_progress' => 'text-bg-primary',
            'completed' => 'text-bg-success',
            default => 'text-bg-secondary',
        };
    }

    public function priorityBadgeClass(): string
    {
        return match ($this->priority) {
            'high' => 'text-bg-danger',
            'medium' => 'text-bg-warning',
            default => 'text-bg-info',
        };
    }
}
