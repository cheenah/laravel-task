<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RolesSeeder;
use Database\Seeders\TestUsersSeeder;

class RoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesSeeder::class);
        $this->seed(TestUsersSeeder::class);
    }

    public function test_users_have_correct_roles()
    {
        $admin = User::where('email', 'admin@example.com')->first();
        $this->assertTrue($admin->hasRole('admin'));

        $manager = User::where('email', 'manager@example.com')->first();
        $this->assertTrue($manager->hasRole('manager'));
        $this->assertFalse($manager->hasRole('admin'));

        $user = User::where('email', 'user@example.com')->first();
        $this->assertTrue($user->hasRole('user'));
    }
}
