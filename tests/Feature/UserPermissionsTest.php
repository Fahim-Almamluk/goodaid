<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPermissionsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test user
        $this->user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /** @test */
    public function it_can_display_user_permissions_page()
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/user-permissions');

        $response->assertStatus(200);
        $response->assertViewIs('admin.user-permissions.index');
    }

    /** @test */
    public function it_can_search_users()
    {
        User::factory()->create(['name' => 'Ahmed Ali', 'email' => 'ahmed@example.com']);
        User::factory()->create(['name' => 'Mohammed Hassan', 'email' => 'mohammed@example.com']);

        $response = $this->actingAs($this->user)
            ->get('/admin/api/users?q=Ahmed');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [['id', 'name', 'email']]]);
        $response->assertJsonCount(1, 'data');
    }

    /** @test */
    public function it_can_get_user_permissions()
    {
        $targetUser = User::factory()->create();
        
        $permission1 = Permission::create([
            'key' => 'test.permission1',
            'label' => 'Test Permission 1',
            'module' => 'Test Module',
            'order' => 1,
        ]);

        $permission2 = Permission::create([
            'key' => 'test.permission2',
            'label' => 'Test Permission 2',
            'module' => 'Test Module',
            'order' => 2,
        ]);

        $targetUser->permissions()->attach($permission1->id);

        $response = $this->actingAs($this->user)
            ->get("/admin/api/user-permissions?user_id={$targetUser->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user' => ['id', 'name', 'email'],
            'permissions' => [['id', 'key', 'label', 'module']],
            'assigned_ids',
        ]);

        $data = $response->json();
        $this->assertCount(2, $data['permissions']);
        $this->assertContains($permission1->id, $data['assigned_ids']);
        $this->assertNotContains($permission2->id, $data['assigned_ids']);
    }

    /** @test */
    public function it_can_filter_permissions_by_status()
    {
        $targetUser = User::factory()->create();
        
        $permission1 = Permission::create([
            'key' => 'test.permission1',
            'label' => 'Test Permission 1',
            'module' => 'Test Module',
            'order' => 1,
        ]);

        $permission2 = Permission::create([
            'key' => 'test.permission2',
            'label' => 'Test Permission 2',
            'module' => 'Test Module',
            'order' => 2,
        ]);

        $targetUser->permissions()->attach($permission1->id);

        // Test assigned filter
        $response = $this->actingAs($this->user)
            ->get("/admin/api/user-permissions?user_id={$targetUser->id}&status=assigned");

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data['permissions']);
        $this->assertEquals($permission1->id, $data['permissions'][0]['id']);

        // Test unassigned filter
        $response = $this->actingAs($this->user)
            ->get("/admin/api/user-permissions?user_id={$targetUser->id}&status=unassigned");

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data['permissions']);
        $this->assertEquals($permission2->id, $data['permissions'][0]['id']);
    }

    /** @test */
    public function it_can_search_permissions()
    {
        $targetUser = User::factory()->create();
        
        Permission::create([
            'key' => 'test.permission1',
            'label' => 'View Test',
            'module' => 'Test Module',
            'order' => 1,
        ]);

        Permission::create([
            'key' => 'test.permission2',
            'label' => 'Edit Test',
            'module' => 'Test Module',
            'order' => 2,
        ]);

        $response = $this->actingAs($this->user)
            ->get("/admin/api/user-permissions?user_id={$targetUser->id}&q=View");

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertCount(1, $data['permissions']);
        $this->assertStringContainsString('View', $data['permissions'][0]['label']);
    }

    /** @test */
    public function it_can_sync_user_permissions()
    {
        $targetUser = User::factory()->create();
        
        $permission1 = Permission::create([
            'key' => 'test.permission1',
            'label' => 'Test Permission 1',
            'module' => 'Test Module',
            'order' => 1,
        ]);

        $permission2 = Permission::create([
            'key' => 'test.permission2',
            'label' => 'Test Permission 2',
            'module' => 'Test Module',
            'order' => 2,
        ]);

        $permission3 = Permission::create([
            'key' => 'test.permission3',
            'label' => 'Test Permission 3',
            'module' => 'Test Module',
            'order' => 3,
        ]);

        // Initially assign permission1
        $targetUser->permissions()->attach($permission1->id);

        // Sync: give permission2, revoke permission1
        $response = $this->actingAs($this->user)
            ->patch('/admin/api/user-permissions/sync', [
                'user_id' => $targetUser->id,
                'give_ids' => [$permission2->id, $permission3->id],
                'revoke_ids' => [$permission1->id],
            ]);

        $response->assertStatus(200);
        $response->assertJson(['ok' => true]);
        $response->assertJsonStructure(['assigned_count']);

        // Verify permissions
        $this->assertTrue($targetUser->permissions()->where('permissions.id', $permission2->id)->exists());
        $this->assertTrue($targetUser->permissions()->where('permissions.id', $permission3->id)->exists());
        $this->assertFalse($targetUser->permissions()->where('permissions.id', $permission1->id)->exists());
    }

    /** @test */
    public function it_validates_user_id_when_getting_permissions()
    {
        $response = $this->actingAs($this->user)
            ->get('/admin/api/user-permissions');

        $response->assertStatus(422);
    }

    /** @test */
    public function it_validates_user_id_when_syncing_permissions()
    {
        $response = $this->actingAs($this->user)
            ->patch('/admin/api/user-permissions/sync', [
                'give_ids' => [1],
                'revoke_ids' => [],
            ]);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_validates_permission_ids_when_syncing()
    {
        $targetUser = User::factory()->create();

        $response = $this->actingAs($this->user)
            ->patch('/admin/api/user-permissions/sync', [
                'user_id' => $targetUser->id,
                'give_ids' => [99999], // Non-existent permission
                'revoke_ids' => [],
            ]);

        $response->assertStatus(422);
    }
}
