<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\CreatesTestTables;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use CreatesTestTables;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestTables();
    }

    private function validData(array $override = []): array
    {
        return array_merge([
            'username' => 'truong',
            'email' => 'truong@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ], $override);
    }

    public function test_register_with_valid_data_hashes_password_and_redirects_to_login(): void
    {
        $this->post('/register', $this->validData())->assertRedirect(route('login'));

        $user = User::where('email', 'truong@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotSame('password123', $user->password);
        $this->assertSame('user', $user->role);
    }

    public function test_register_cannot_create_admin_by_sending_role(): void
    {
        $this->post('/register', $this->validData(['role' => 'admin']))->assertRedirect(route('login'));

        $this->assertSame('user', User::where('email', 'truong@example.com')->value('role'));
    }

    public function test_register_trims_username_and_email(): void
    {
        $this->post('/register', $this->validData(['username' => '  truong  ', 'email' => '  Truong@Example.com ']));

        $this->assertDatabaseHas('users', ['username' => 'truong', 'email' => 'truong@example.com']);
    }

    public function test_register_rejects_duplicate_username_and_email(): void
    {
        User::factory()->create(['username' => 'truong', 'email' => 'truong@example.com']);

        $this->post('/register', $this->validData())->assertSessionHasErrors(['username', 'email']);
    }

    public function test_register_rejects_short_password_and_mismatch(): void
    {
        $this->post('/register', $this->validData(['password' => 'short', 'password_confirmation' => 'other']))
            ->assertSessionHasErrors('password');
    }

    public function test_login_success_and_failure(): void
    {
        $user = User::factory()->create(['email' => 'a@example.com']);

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'wrong-pass'])
            ->assertSessionHasErrors('login');
        $this->assertGuest();

        $this->post('/login', ['email' => 'a@example.com', 'password' => 'password123'])
            ->assertRedirect(route('tasks.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_logout_requires_post_and_logs_out(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/logout')->assertStatus(405);
        $this->actingAs($user)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_guest_cannot_open_task_pages(): void
    {
        $this->get('/tasks')->assertRedirect(route('login'));
        $this->get('/tasks/create')->assertRedirect(route('login'));
    }
}
