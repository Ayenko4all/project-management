<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $role = Role::where('name', 'Admin')->pluck('id')->first();
        $user = User::factory()->create(['email' =>  "admin@gmail.com"]);
        $user->roles()->syncWithoutDetaching($role);
    }
}
