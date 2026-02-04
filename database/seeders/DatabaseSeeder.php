<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $department = Department::create(['department_name' => 'IT']);
        
        $role = Role::create([
            'role' => 'admin', 
            'description' => 'Administrator',
            'is_active' => 1
        ]);

        User::create([
            'username' => 'alwani',
            'email' => 'alwani@example.com',
            'password' => Hash::make('password'),
            'role_id' => $role->id,
            'department_id' => $department->id,
            'is_active' => 1,
        ]);
    }
}
