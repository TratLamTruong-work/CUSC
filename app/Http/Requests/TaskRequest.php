<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

/**
 * Dùng chung cho thêm (POST /tasks) và sửa (PUT /tasks/{id}).
 * Không có user_id / role trong rules => không bao giờ nhận từ client.
 */
class TaskRequest extends FormRequest
{
    use VietnameseMessages;

    private ?Task $existingTask = null;

    // [BỔ SUNG] Kiểm tra quyền (Policy) trước validation khi sửa: task không tồn tại hoặc
    // không thuộc phạm vi quyền của User -> 404.
    public function authorize(): bool
    {
        if ($this->route('id') !== null) {
            $this->existingTask = Task::findOrFail($this->route('id'));
            Gate::authorize('update', $this->existingTask);
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        $description = $this->input('description');
        $description = is_string($description) ? trim($description) : null;

        $this->merge([
            'title' => is_string($this->title) ? trim($this->title) : $this->title,
            'description' => $description === '' ? null : $description,
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'due_date' => ['bail', 'required', 'date_format:Y-m-d', $this->dueDateRule()],
            'priority' => ['required', Rule::in(array_keys(Task::PRIORITIES))],
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
        ];
    }

    // [BỔ SUNG] Quy tắc ngày:
    //  - luôn: hạn chót không trước ngày bắt đầu;
    //  - thêm mới: hạn chót từ hôm nay trở đi;
    //  - sửa: giữ nguyên hạn chót cũ (đã qua) vẫn được; nếu đổi thì phải từ hôm nay trở đi.
    private function dueDateRule(): \Closure
    {
        return function (string $attribute, mixed $value, \Closure $fail) {
            $start = $this->input('start_date');
            $startIsValid = is_string($start) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) === 1;

            if ($startIsValid && $value < $start) {
                $fail('Hạn chót không được trước ngày bắt đầu.');

                return;
            }

            $unchanged = $this->existingTask !== null
                && $value === $this->existingTask->due_date->format('Y-m-d');

            if (! $unchanged && $value < Carbon::today()->toDateString()) {
                $fail('Hạn chót phải từ hôm nay trở đi.');
            }
        };
    }

    public function attributes(): array
    {
        return [
            'title' => 'Tên công việc',
            'description' => 'Mô tả',
            'start_date' => 'Ngày bắt đầu',
            'due_date' => 'Hạn chót',
            'priority' => 'Mức độ ưu tiên',
            'status' => 'Trạng thái',
        ];
    }
}
