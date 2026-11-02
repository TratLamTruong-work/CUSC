@extends('layouts.app')
@section('title', 'Danh sách công việc')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h3 mb-0">Danh sách công việc</h1>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">Thêm công việc</a>
</div>

{{-- [BỔ SUNG] Thống kê: tính trên toàn bộ task của người dùng, không đổi theo bộ lọc --}}
<div class="row row-cols-2 row-cols-md-4 g-3 mb-2">
    <div class="col"><div class="stat-box"><div class="text-muted">Tổng công việc</div><div class="stat-number">{{ $stats['total'] }}</div></div></div>
    <div class="col"><div class="stat-box"><div class="text-muted">Chưa làm</div><div class="stat-number">{{ $stats['pending'] }}</div></div></div>
    <div class="col"><div class="stat-box"><div class="text-muted">Đang làm</div><div class="stat-number text-primary">{{ $stats['in_progress'] }}</div></div></div>
    <div class="col"><div class="stat-box"><div class="text-muted">Hoàn thành</div><div class="stat-number text-success">{{ $stats['completed'] }}</div></div></div>
</div>
<p class="text-muted small mb-3">
    @if ($isAdmin)
        Số liệu tính trên tất cả công việc trong hệ thống, không thay đổi theo bộ lọc bên dưới.
    @else
        Số liệu tính trên tất cả công việc của bạn, không thay đổi theo bộ lọc bên dưới.
    @endif
</p>

<form method="GET" action="{{ route('tasks.index') }}" class="panel">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-lg-4">
            <label for="keyword" class="form-label">Từ khóa</label>
            <input type="search" id="keyword" name="keyword" value="{{ $filters['keyword'] }}" maxlength="255"
                   class="form-control" placeholder="Tên hoặc mô tả công việc">
        </div>
        <div class="col-6 col-lg-2">
            <label for="status" class="form-label">Trạng thái</label>
            <select id="status" name="status" class="form-select">
                <option value="">Tất cả</option>
                @foreach (\App\Models\Task::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected($filters['status'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-lg-2">
            <label for="priority" class="form-label">Ưu tiên</label>
            <select id="priority" name="priority" class="form-select">
                <option value="">Tất cả</option>
                @foreach (\App\Models\Task::PRIORITIES as $value => $label)
                    <option value="{{ $value }}" @selected($filters['priority'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        @if ($isAdmin)
            <div class="col-12 col-md-6 col-lg-4">
                <label for="owner" class="form-label">Người sở hữu</label>
                <select id="owner" name="owner" class="form-select">
                    <option value="">Tất cả người dùng</option>
                    @foreach ($owners as $owner)
                        <option value="{{ $owner->id }}" @selected($filters['owner'] === $owner->id)>{{ $owner->username }}</option>
                    @endforeach
                </select>
            </div>
        @endif
        <div class="col-12 col-md-6 col-lg-4">
            <label for="sort" class="form-label">Sắp xếp</label>
            <select id="sort" name="sort" class="form-select">
                <option value="newest" @selected($filters['sort'] === 'newest')>Mới nhất</option>
                <option value="due" @selected($filters['sort'] === 'due')>Hạn chót gần nhất</option>
            </select>
        </div>
        <div class="col-12 d-flex flex-wrap gap-2">
            <button type="submit" class="btn btn-primary">Tìm kiếm</button>
            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
        </div>
    </div>
</form>

<div class="panel p-0 overflow-hidden">
    @if ($tasks->isEmpty())
        <div class="text-center p-5">
            @if ($stats['total'] === 0)
                <p class="fs-5 mb-3">{{ $isAdmin ? 'Hệ thống chưa có công việc nào' : 'Bạn chưa có công việc nào' }}</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary">Thêm công việc</a>
            @else
                <p class="fs-5 mb-3">Không tìm thấy công việc phù hợp</p>
                <a href="{{ route('tasks.index') }}" class="btn btn-outline-primary">Xóa bộ lọc</a>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                <tr>
                    <th scope="col">STT</th>
                    <th scope="col">Tên công việc</th>
                    @if ($isAdmin)
                        <th scope="col">Người sở hữu</th>
                    @endif
                    <th scope="col">Ngày bắt đầu</th>
                    <th scope="col">Hạn chót</th>
                    <th scope="col">Ưu tiên</th>
                    <th scope="col">Trạng thái</th>
                    <th scope="col">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($tasks as $task)
                    <tr>
                        <td>{{ $tasks->firstItem() + $loop->index }}</td>
                        <td class="task-title">
                            <a href="{{ route('tasks.show', $task->id) }}" class="fw-semibold text-decoration-none"
                               title="{{ $task->title }}">{{ \Illuminate\Support\Str::limit($task->title, 100) }}</a>
                        </td>
                        @if ($isAdmin)
                            <td class="text-nowrap">{{ $task->user->username }}</td>
                        @endif
                        <td class="text-nowrap">{{ $task->start_date->format('d/m/Y') }}</td>
                        <td class="text-nowrap">
                            {{ $task->due_date->format('d/m/Y') }}
                            {{-- [BỔ SUNG] Nhãn quá hạn, tính từ ngày và trạng thái --}}
                            @if ($task->isOverdue())
                                <span class="badge badge-overdue ms-1">Quá hạn</span>
                            @endif
                        </td>
                        <td><span class="badge {{ $task->priorityBadgeClass() }}">{{ $task->priorityLabel() }}</span></td>
                        <td>
                            <span class="badge {{ $task->statusBadgeClass() }}">{{ $task->statusLabel() }}</span>
                            <form method="POST" action="{{ route('tasks.status', $task->id) }}" class="d-flex gap-1 mt-1">
                                @csrf
                                @method('PATCH')
                                <label for="status-{{ $task->id }}" class="visually-hidden">Đổi trạng thái công việc số {{ $tasks->firstItem() + $loop->index }}</label>
                                <select id="status-{{ $task->id }}" name="status" class="form-select form-select-sm status-select">
                                    @foreach (\App\Models\Task::STATUSES as $value => $label)
                                        <option value="{{ $value }}" @selected($task->status === $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Lưu</button>
                            </form>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('tasks.show', $task->id) }}" class="btn btn-sm btn-outline-secondary">Xem</a>
                                <a href="{{ route('tasks.edit', $task->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <form method="POST" action="{{ route('tasks.destroy', $task->id) }}" class="m-0"
                                      data-confirm="Bạn có chắc muốn xóa công việc này? Hành động không thể hoàn tác.">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 border-top">
            <span>Tìm thấy <strong>{{ $tasks->total() }}</strong> công việc</span>
            {{ $tasks->links('pagination') }}
        </div>
    @endif
</div>
@endsection
