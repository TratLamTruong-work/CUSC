<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class TaskStatusRequest extends FormRequest
{
    use VietnameseMessages;

    // [BỔ SUNG] Admin đổi được trạng thái mọi task; User chỉ task của mình, ngược lại 404.
    // Task quá hạn vẫn được đổi trạng thái (không có kiểm tra ngày ở đây).
    public function authorize(): bool
    {
        Gate::authorize('update', Task::findOrFail($this->route('id')));

        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(array_keys(Task::STATUSES))],
        ];
    }

    public function attributes(): array
    {
        return ['status' => 'Trạng thái'];
    }
}
