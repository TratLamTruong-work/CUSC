<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskRequest;
use App\Http\Requests\TaskStatusRequest;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TaskController extends Controller
{
    private const PER_PAGE = 10;

    private const SORTS = ['newest', 'due'];

    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        // Chuẩn hóa tham số GET: chỉ nhận giá trị nằm trong danh sách cho phép.
        $keyword = $request->query('keyword');
        $keyword = is_string($keyword) ? mb_substr(trim($keyword), 0, 255) : '';

        $status = $request->query('status');
        $status = is_string($status) && array_key_exists($status, Task::STATUSES) ? $status : '';

        $priority = $request->query('priority');
        $priority = is_string($priority) && array_key_exists($priority, Task::PRIORITIES) ? $priority : '';

        $sort = $request->query('sort');
        $sort = is_string($sort) && in_array($sort, self::SORTS, true) ? $sort : 'newest';

        // [BỔ SUNG] Lọc theo chủ sở hữu: chỉ Admin. Với User, tham số owner bị bỏ qua hoàn toàn.
        $owners = collect();
        $ownerId = '';
        if ($user->isAdmin()) {
            $owners = User::orderBy('username')->get(['id', 'username']);
            $owner = $request->query('owner');
            if (is_string($owner) && ctype_digit($owner) && $owners->contains('id', (int) $owner)) {
                $ownerId = (int) $owner;
            }
        }

        // [BỔ SUNG] Phạm vi quyền: User chỉ task của mình, Admin toàn hệ thống.
        // Mọi điều kiện tìm kiếm/lọc bên dưới chỉ được thêm vào truy vấn đã giới hạn này.
        $query = $this->visibleTasks($user)->with('user:id,username');

        if ($keyword !== '') {
            $like = '%'.addcslashes($keyword, '%_\\').'%';
            // Nhóm (tên OR mô tả) trong một cặp ngoặc, nằm dưới điều kiện phạm vi quyền.
            $query->where(function ($q) use ($like) {
                $q->where('title', 'like', $like)->orWhere('description', 'like', $like);
            });
        }
        if ($status !== '') {
            $query->where('status', $status);
        }
        if ($priority !== '') {
            $query->where('priority', $priority);
        }
        if ($ownerId !== '') {
            $query->where('user_id', $ownerId);
        }

        if ($sort === 'due') {
            $query->orderBy('due_date')->orderByDesc('id');
        } else {
            $query->orderByDesc('created_at')->orderByDesc('id');
        }

        $tasks = $query->paginate(self::PER_PAGE)->withQueryString();

        // [BỔ SUNG] Trang rỗng (ví dụ vừa xóa bản ghi cuối của trang) -> chuyển về trang cuối hợp lệ.
        if ($tasks->isEmpty() && $tasks->currentPage() > 1) {
            return redirect()->route('tasks.index', array_merge($request->query(), ['page' => $tasks->lastPage()]));
        }

        // Thống kê theo phạm vi quyền, không phụ thuộc bộ lọc.
        $counts = $this->visibleTasks($user)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'total' => (int) $counts->sum(),
            'pending' => (int) $counts->get('pending', 0),
            'in_progress' => (int) $counts->get('in_progress', 0),
            'completed' => (int) $counts->get('completed', 0),
        ];

        return view('tasks.index', [
            'tasks' => $tasks,
            'stats' => $stats,
            'owners' => $owners,
            'isAdmin' => $user->isAdmin(),
            'filters' => [
                'keyword' => $keyword,
                'status' => $status,
                'priority' => $priority,
                'owner' => $ownerId,
                'sort' => $sort,
            ],
        ]);
    }

    public function create(): View
    {
        return view('tasks.create', [
            'task' => new Task(['priority' => 'medium', 'status' => 'pending']),
        ]);
    }

    public function store(TaskRequest $request): RedirectResponse
    {
        // user_id luôn là người đang đăng nhập (kể cả Admin), không nhận từ form.
        $request->user()->tasks()->create($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Đã thêm công việc.');
    }

    public function show(string $id): View
    {
        return view('tasks.show', ['task' => $this->findTask($id, 'view')->load('user:id,username')]);
    }

    public function edit(string $id): View
    {
        return view('tasks.edit', ['task' => $this->findTask($id, 'update')]);
    }

    public function update(TaskRequest $request, string $id): RedirectResponse
    {
        // validated() không có user_id => chủ sở hữu giữ nguyên, kể cả khi Admin sửa task của người khác.
        $this->findTask($id, 'update')->update($request->validated());

        return redirect()->route('tasks.index')->with('success', 'Đã cập nhật công việc.');
    }

    public function updateStatus(TaskStatusRequest $request, string $id): RedirectResponse
    {
        $this->findTask($id, 'update')->update(['status' => $request->validated('status')]);

        return back()->with('success', 'Đã cập nhật trạng thái.');
    }

    public function destroy(string $id): RedirectResponse
    {
        $this->findTask($id, 'delete')->delete();

        // back(): quay lại đúng trang danh sách (giữ bộ lọc); index() tự lùi trang nếu trang đó đã rỗng.
        return back()->with('success', 'Đã xóa công việc.');
    }

    // [BỔ SUNG] Phạm vi dữ liệu theo vai trò.
    private function visibleTasks(User $user): Builder
    {
        return $user->isAdmin() ? Task::query() : Task::query()->where('user_id', $user->id);
    }

    // [BỔ SUNG] Không tồn tại -> 404; không có quyền (User xem task người khác) -> 404 qua TaskPolicy.
    private function findTask(string $id, string $ability): Task
    {
        $task = Task::findOrFail($id);
        Gate::authorize($ability, $task);

        return $task;
    }
}
