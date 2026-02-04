<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_receive_cookies()
    {
        $department = Department::create(['department_name' => 'IT']);
        $role = Role::create(['role' => 'admin']);
        
        $user = User::create([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'department_id' => $department->id,
        ]);

        $response = $this->postJson('/api/login', [
            'username' => 'testuser',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user'])
            ->assertCookie('HWE_PUSS')
            ->assertCookie('HWE_USS');
    }
}
