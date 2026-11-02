<?php

namespace Tests\Concerns;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ứng dụng tạo bảng bằng database/sql/schema.sql (MySQL, chạy trong Workbench) nên không có migration.
 * Test dùng SQLite in-memory và tạo bảng tương đương ngay trong test.
 * Nếu sửa schema.sql, nhớ sửa file này cho khớp.
 */
trait CreatesTestTables
{
    protected function createTestTables(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('user');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('due_date');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }
}
