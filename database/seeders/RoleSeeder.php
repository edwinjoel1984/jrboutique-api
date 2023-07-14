<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rolesArray = [['name' => 'admin'], ['name' => 'seller']];
        foreach ($rolesArray as $role) {
            Role::factory()->create($role);
        }
    }
}
