<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAccessTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var User
     */
    private $admin;

    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $manager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutExceptionHandling();

        $this->seed([RoleSeeder::class]);

        $this->user = User::factory()->create();

        $this->admin = User::factory()->create();

        $this->manager = User::factory()->create();

    }

    public function test_only_user_with_admin_role_can_attached_roles(): void
    {
        $this->admin->assignRoleByName("Admin");

        $this->actingAs($this->admin, 'sanctum');

        $form = ['role' => 'Manager'];

        $this->postJson(route('user.assignRole', $this->user->id), $form)
            ->assertOk();

        $this->assertTrue($this->user->hasRole('Manager'));
    }

    public function test_user_without_admin_role_cannot_assign_roles(): void
    {
        $this->manager->assignRoleByName("Manager");

        $form = ['role' => 'Manager'];

       $this->actingAs($this->manager, 'sanctum')
            ->postJson(route('user.assignRole', $this->user->id), $form)
            ->assertStatus(401);
    }
}
