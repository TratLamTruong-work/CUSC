@php
    $startValue = old('start_date', $task->start_date?->format('Y-m-d') ?? now()->toDateString());
    $dueValue = old('due_date', $task->due_date?->format('Y-m-d'));
    $priorityValue = old('priority', $task->priority ?? 'medium');
    $statusValue = old('status', $task->status ?? 'pending');
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @if ($task->exists)
        @method('PUT')
    @endif

    <p class="text-muted small">Trường có dấu <span class="text-danger">*</span> là bắt buộc.</p>

    <div class="mb-3">
        <label for="title" class="form-label">Tên công việc <span class="text-danger" aria-hidden="true">*</span></label>
        <input type="text" id="title" name="title" value="{{ old('title', $task->title) }}" maxlength="255" required
               class="form-control @error('title') is-invalid @enderror">
        @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Mô tả</label>
        <textarea id="description" name="description" rows="5" maxlength="5000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $task->description) }}</textarea>
        @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="row g-3 mb-3">
        <div class="col-12 col-md-6">
            <label for="start_date" class="form-label">Ngày bắt đầu <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="date" id="start_date" name="start_date" value="{{ $startValue }}" required
                   class="form-control @error('start_date') is-invalid @enderror">
            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="due_date" class="form-label">Hạn chót <span class="text-danger" aria-hidden="true">*</span></label>
            <input type="date" id="due_date" name="due_date" value="{{ $dueValue }}" required
                   class="form-control @error('due_date') is-invalid @enderror">
            @error('due_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 col-md-6">
            <label for="priority" class="form-label">Mức độ ưu tiên <span class="text-danger" aria-hidden="true">*</span></label>
            <select id="priority" name="priority" required class="form-select @error('priority') is-invalid @enderror">
                @foreach (\App\Models\Task::PRIORITIES as $value => $label)
                    <option value="{{ $value }}" @selected($priorityValue === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('priority') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-md-6">
            <label for="status" class="form-label">Trạng thái <span class="text-danger" aria-hidden="true">*</span></label>
            <select id="status" name="status" required class="form-select @error('status') is-invalid @enderror">
                @foreach (\App\Models\Task::STATUSES as $value => $label)
                    <option value="{{ $value }}" @selected($statusValue === $value)>{{ $label }}</option>
                @endforeach
            </select>
            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <button type="submit" class="btn btn-primary">Lưu công việc</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Hủy</a>
    </div>
</form>
