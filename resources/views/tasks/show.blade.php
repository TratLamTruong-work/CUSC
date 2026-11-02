@extends('layouts.app')
@section('title', 'Chi tiết công việc')

@section('content')
<div class="panel">
    <h1 class="h3 mb-4 text-break">{{ $task->title }}</h1>

    <dl class="row mb-4">
        <dt class="col-sm-3">Mô tả</dt>
        <dd class="col-sm-9 pre-line">{{ $task->description ?: 'Chưa có mô tả' }}</dd>

        <dt class="col-sm-3">Người sở hữu</dt>
        <dd class="col-sm-9">{{ $task->user->username }}</dd>

        <dt class="col-sm-3">Ngày bắt đầu</dt>
        <dd class="col-sm-9">{{ $task->start_date->format('d/m/Y') }}</dd>

        <dt class="col-sm-3">Hạn chót</dt>
        <dd class="col-sm-9">
            {{ $task->due_date->format('d/m/Y') }}
            @if ($task->isOverdue())
                <span class="badge badge-overdue ms-1">Quá hạn</span>
            @endif
        </dd>

        <dt class="col-sm-3">Mức độ ưu tiên</dt>
        <dd class="col-sm-9"><span class="badge {{ $task->priorityBadgeClass() }}">{{ $task->priorityLabel() }}</span></dd>

        <dt class="col-sm-3">Trạng thái</dt>
        <dd class="col-sm-9"><span class="badge {{ $task->statusBadgeClass() }}">{{ $task->statusLabel() }}</span></dd>

        <dt class="col-sm-3">Ngày tạo</dt>
        <dd class="col-sm-9">{{ $task->created_at->format('d/m/Y H:i') }}</dd>

        <dt class="col-sm-3">Cập nhật lần cuối</dt>
        <dd class="col-sm-9">{{ $task->updated_at->format('d/m/Y H:i') }}</dd>
    </dl>

    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Quay lại danh sách</a>
        <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-primary">Sửa công việc</a>
    </div>
</div>
@endsection
