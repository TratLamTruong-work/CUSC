<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Carbon;
use Tests\Concerns\CreatesTestTables;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use CreatesTestTables;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTables();
    }

    private function makeTask(User $user, array $override = []): Task
    {
        return $user->tasks()->create(array_merge([
            'title' => 'Công việc',
            'description' => null,
            'start_date' => Carbon::today()->toDateString(),
            'due_date' => Carbon::today()->addDays(3)->toDateString(),
            'priority' => 'medium',
            'status' => 'pending',
        ], $override));
    }

    private function payload(array $override = []): array
    {
        return array_merge([
            'title' => 'Việc mới',
            'description' => 'Mô tả',
            'start_date' => Carbon::today()->toDateString(),
            'due_date' => Carbon::today()->addDays(2)->toDateString(),
            'priority' => 'medium',
            'status' => 'pending',
        ], $override);
    }

    // ---------- Phân quyền User ----------

    public function test_user_cannot_access_or_change_other_users_task(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $task = $this->makeTask($a);

        $this->actingAs($b)->get("/tasks/{$task->id}")->assertNotFound();
        $this->actingAs($b)->get("/tasks/{$task->id}/edit")->assertNotFound();
        $this->actingAs($b)->put("/tasks/{$task->id}", $this->payload())->assertNotFound();
        $this->actingAs($b)->patch("/tasks/{$task->id}/status", ['status' => 'completed'])->assertNotFound();
        $this->actingAs($b)->delete("/tasks/{$task->id}")->assertNotFound();

        $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => 'pending']);
    }

    public function test_nonexistent_task_returns_404(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/tasks/99999')->assertNotFound();
        $this->actingAs($user)->delete('/tasks/99999')->assertNotFound();
    }

    public function test_user_id_and_role_from_request_are_ignored(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();

        $this->actingAs($a)->post('/tasks', $this->payload(['user_id' => $b->id]))
            ->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', ['title' => 'Việc mới', 'user_id' => $a->id]);
    }

    public function test_user_owner_param_in_url_does_not_widen_scope(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeTask($a, ['title' => 'của A']);
        $this->makeTask($b, ['title' => 'của B']);

        $this->actingAs($a)->get("/tasks?owner={$b->id}&user_id={$b->id}")
            ->assertSee('của A')
            ->assertDontSee('của B')
            ->assertDontSee('Tất cả người dùng');
    }

    // ---------- Phân quyền Admin ----------

    public function test_admin_can_view_edit_change_status_and_delete_any_task(): void
    {
        $admin = User::factory()->admin()->create();
        $a = User::factory()->create();
        $task = $this->makeTask($a, ['title' => 'task của A']);

        $this->actingAs($admin)->get("/tasks/{$task->id}")->assertOk()->assertSee('task của A');
        $this->actingAs($admin)->get("/tasks/{$task->id}/edit")->assertOk();

        $this->actingAs($admin)->put("/tasks/{$task->id}", $this->payload(['title' => 'Admin sửa']))
            ->assertRedirect(route('tasks.index'));
        $fresh = $task->fresh();
        $this->assertSame('Admin sửa', $fresh->title);
        $this->assertSame($a->id, $fresh->user_id); // chủ sở hữu giữ nguyên

        $this->actingAs($admin)->patch("/tasks/{$task->id}/status", ['status' => 'completed'])->assertRedirect();
        $this->assertSame('completed', $task->fresh()->status);

        $this->actingAs($admin)->delete("/tasks/{$task->id}")->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_admin_edit_ignores_user_id_in_request(): void
    {
        $admin = User::factory()->admin()->create();
        $a = User::factory()->create();
        $task = $this->makeTask($a);

        $this->actingAs($admin)->put("/tasks/{$task->id}", $this->payload(['user_id' => $admin->id]));

        $this->assertSame($a->id, $task->fresh()->user_id);
    }

    public function test_admin_creates_task_for_self(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/tasks', $this->payload());

        $this->assertDatabaseHas('tasks', ['title' => 'Việc mới', 'user_id' => $admin->id]);
    }

    public function test_admin_sees_all_tasks_and_can_filter_by_owner(): void
    {
        $admin = User::factory()->admin()->create();
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeTask($a, ['title' => 'của A']);
        $this->makeTask($b, ['title' => 'của B']);

        $this->actingAs($admin)->get('/tasks')
            ->assertSee('của A')->assertSee('của B')->assertSee('Tất cả người dùng');

        $this->actingAs($admin)->get("/tasks?owner={$a->id}")
            ->assertSee('của A')->assertDontSee('của B');
    }

    public function test_stats_scope_by_role_and_ignore_filters(): void
    {
        $admin = User::factory()->admin()->create();
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeTask($a, ['status' => 'pending']);
        $this->makeTask($a, ['status' => 'completed']);
        $this->makeTask($b, ['status' => 'completed']);

        $this->actingAs($a)->get('/tasks?status=pending')
            ->assertViewHas('stats', ['total' => 2, 'pending' => 1, 'in_progress' => 0, 'completed' => 1]);

        $this->actingAs($admin)->get('/tasks?status=pending')
            ->assertViewHas('stats', ['total' => 3, 'pending' => 1, 'in_progress' => 0, 'completed' => 2]);
    }

    // ---------- CRUD ----------

    public function test_crud_flow_for_owner(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', $this->payload())->assertRedirect(route('tasks.index'));
        $task = Task::firstOrFail();

        $this->actingAs($user)->put("/tasks/{$task->id}", $this->payload(['title' => 'Đã sửa']))
            ->assertRedirect(route('tasks.index'));
        $this->assertSame('Đã sửa', $task->fresh()->title);

        $this->actingAs($user)->patch("/tasks/{$task->id}/status", ['status' => 'completed'])->assertRedirect();
        $this->assertSame('completed', $task->fresh()->status);

        $this->actingAs($user)->patch("/tasks/{$task->id}/status", ['status' => 'hack'])
            ->assertSessionHasErrors('status');

        $this->actingAs($user)->delete("/tasks/{$task->id}")->assertRedirect();
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    public function test_status_endpoint_only_changes_status_and_works_for_overdue_task(): void
    {
        $user = User::factory()->create();
        $task = $this->makeTask($user, [
            'title' => 'Giữ nguyên',
            'start_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => Carbon::today()->subDays(2)->toDateString(),
        ]);

        $this->actingAs($user)->patch("/tasks/{$task->id}/status", ['status' => 'in_progress', 'title' => 'Hack'])
            ->assertRedirect();

        $fresh = $task->fresh();
        $this->assertSame('in_progress', $fresh->status);
        $this->assertSame('Giữ nguyên', $fresh->title);
    }

    public function test_title_is_trimmed_and_whitespace_only_title_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', $this->payload(['title' => '   ']))
            ->assertSessionHasErrors('title');

        $this->actingAs($user)->post('/tasks', $this->payload(['title' => '  Có khoảng trắng  ']));
        $this->assertDatabaseHas('tasks', ['title' => 'Có khoảng trắng']);
    }

    public function test_invalid_priority_and_status_are_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', $this->payload(['priority' => 'urgent', 'status' => 'done']))
            ->assertSessionHasErrors(['priority', 'status']);
    }

    // ---------- Quy tắc ngày ----------

    public function test_create_rejects_past_due_date_and_due_before_start(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->post('/tasks', $this->payload([
            'start_date' => Carbon::today()->subDays(5)->toDateString(),
            'due_date' => Carbon::today()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('due_date');

        $this->actingAs($user)->post('/tasks', $this->payload([
            'start_date' => Carbon::today()->addDays(5)->toDateString(),
            'due_date' => Carbon::today()->addDays(2)->toDateString(),
        ]))->assertSessionHasErrors('due_date');

        // Ngày bắt đầu trong quá khứ vẫn được phép, hạn chót hôm nay hợp lệ
        $this->actingAs($user)->post('/tasks', $this->payload([
            'start_date' => Carbon::today()->subDays(3)->toDateString(),
            'due_date' => Carbon::today()->toDateString(),
        ]))->assertSessionDoesntHaveErrors();
    }

    public function test_update_may_keep_past_due_date_but_not_change_to_a_past_one(): void
    {
        $user = User::factory()->create();
        $pastDue = Carbon::today()->subDays(2)->toDateString();
        $task = $this->makeTask($user, [
            'start_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => $pastDue,
        ]);

        $this->actingAs($user)->put("/tasks/{$task->id}", $this->payload([
            'title' => 'Giữ hạn cũ',
            'start_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => $pastDue,
        ]))->assertSessionDoesntHaveErrors();
        $this->assertSame('Giữ hạn cũ', $task->fresh()->title);

        $this->actingAs($user)->put("/tasks/{$task->id}", $this->payload([
            'start_date' => Carbon::today()->subDays(10)->toDateString(),
            'due_date' => Carbon::today()->subDay()->toDateString(),
        ]))->assertSessionHasErrors('due_date');

        $this->actingAs($user)->put("/tasks/{$task->id}", $this->payload([
            'start_date' => Carbon::today()->toDateString(),
            'due_date' => $pastDue,
        ]))->assertSessionHasErrors('due_date');
    }

    public function test_failed_validation_keeps_user_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->from('/tasks/create')
            ->post('/tasks', $this->payload(['title' => 'Giữ lại', 'priority' => 'bad']))
            ->assertRedirect('/tasks/create')
            ->assertSessionHasInput('title', 'Giữ lại');
    }

    // ---------- Quá hạn ----------

    public function test_overdue_rules(): void
    {
        $user = User::factory()->create();
        $past = $this->makeTask($user, [
            'start_date' => Carbon::today()->subDays(5)->toDateString(),
            'due_date' => Carbon::today()->subDay()->toDateString(),
        ]);
        $todayTask = $this->makeTask($user, ['due_date' => Carbon::today()->toDateString()]);
        $done = $this->makeTask($user, [
            'start_date' => Carbon::today()->subDays(5)->toDateString(),
            'due_date' => Carbon::today()->subDay()->toDateString(),
            'status' => 'completed',
        ]);

        $this->assertTrue($past->isOverdue());
        $this->assertFalse($todayTask->isOverdue());
        $this->assertFalse($done->isOverdue());
    }

    // ---------- Tìm kiếm, lọc, sắp xếp, phân trang ----------

    public function test_user_search_never_returns_other_users_tasks(): void
    {
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeTask($a, ['title' => 'báo cáo của A']);
        $this->makeTask($a, ['title' => 'khác', 'description' => 'có chữ báo cáo trong mô tả']);
        $this->makeTask($b, ['title' => 'báo cáo của B']);

        $this->actingAs($a)->get('/tasks?keyword=báo cáo')
            ->assertOk()
            ->assertSee('báo cáo của A')
            ->assertSee('khác')
            ->assertDontSee('báo cáo của B');
    }

    public function test_admin_search_covers_all_tasks(): void
    {
        $admin = User::factory()->admin()->create();
        $a = User::factory()->create();
        $b = User::factory()->create();
        $this->makeTask($a, ['title' => 'báo cáo của A']);
        $this->makeTask($b, ['title' => 'khác', 'description' => 'báo cáo trong mô tả của B']);

        $this->actingAs($admin)->get('/tasks?keyword=báo cáo')
            ->assertSee('báo cáo của A')
            ->assertSee('khác');
    }

    public function test_keyword_status_and_priority_filters_combine(): void
    {
        $user = User::factory()->create();
        $this->makeTask($user, ['title' => 'khớp hết', 'status' => 'pending', 'priority' => 'high']);
        $this->makeTask($user, ['title' => 'sai ưu tiên', 'status' => 'pending', 'priority' => 'low']);
        $this->makeTask($user, ['title' => 'sai trạng thái', 'status' => 'completed', 'priority' => 'high']);

        $this->actingAs($user)->get('/tasks?keyword=khớp&status=pending&priority=high')
            ->assertSee('khớp hết')
            ->assertDontSee('sai ưu tiên')
            ->assertDontSee('sai trạng thái');

        $this->actingAs($user)->get('/tasks?keyword=sai&status=pending&priority=high')
            ->assertSee('Không tìm thấy công việc phù hợp');
    }

    public function test_sort_by_due_date_and_invalid_sort_falls_back(): void
    {
        $user = User::factory()->create();
        $this->makeTask($user, ['title' => 'TASK-XA-NHAT', 'due_date' => Carbon::today()->addDays(9)->toDateString()]);
        $this->makeTask($user, ['title' => 'TASK-GAN-NHAT', 'due_date' => Carbon::today()->addDay()->toDateString()]);

        $this->actingAs($user)->get('/tasks?sort=due')
            ->assertSeeInOrder(['TASK-GAN-NHAT', 'TASK-XA-NHAT']);

        $this->actingAs($user)->get('/tasks?sort=password;drop table')->assertOk();
    }

    public function test_pagination_has_10_per_page_and_keeps_query_string(): void
    {
        $user = User::factory()->create();
        for ($i = 1; $i <= 12; $i++) {
            $this->makeTask($user, ['title' => "Task $i", 'status' => 'pending']);
        }

        $response = $this->actingAs($user)->get('/tasks?status=pending');
        $response->assertOk()->assertSee('status=pending&amp;page=2', false);
        $this->assertCount(10, $response->viewData('tasks'));

        $this->actingAs($user)->get('/tasks?status=pending&page=2')
            ->assertViewHas('tasks', fn ($tasks) => $tasks->count() === 2);
    }

    public function test_empty_last_page_redirects_to_last_valid_page(): void
    {
        $user = User::factory()->create();
        for ($i = 1; $i <= 11; $i++) {
            $this->makeTask($user);
        }

        $this->actingAs($user)->get('/tasks?page=5')->assertRedirect(route('tasks.index', ['page' => 2]));
    }

    public function test_empty_states_are_different(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/tasks')->assertSee('Bạn chưa có công việc nào');

        $this->makeTask($user, ['title' => 'abc']);
        $this->actingAs($user)->get('/tasks?keyword=zzz')->assertSee('Không tìm thấy công việc phù hợp');

        $admin = User::factory()->admin()->create();
        Task::query()->delete();
        $this->actingAs($admin)->get('/tasks')->assertSee('Hệ thống chưa có công việc nào');
    }

    // ---------- XSS ----------

    public function test_html_in_title_and_description_is_escaped(): void
    {
        $user = User::factory()->create();
        $task = $this->makeTask($user, [
            'title' => '<script>alert(1)</script>',
            'description' => '<img src=x onerror=alert(1)>',
        ]);

        foreach (['/tasks', "/tasks/{$task->id}"] as $url) {
            $this->actingAs($user)->get($url)
                ->assertDontSee('<script>alert(1)</script>', false)
                ->assertDontSee('<img src=x', false);
        }
        $this->actingAs($user)->get("/tasks/{$task->id}")->assertSee('&lt;script&gt;', false);
    }
}
