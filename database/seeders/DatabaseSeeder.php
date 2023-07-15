<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Provider;
use App\Models\Brand;
use App\Models\Role;
use App\Models\Size;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $rolesArray = [['name' => 'admin'], ['name' => 'seller']];
        foreach ($rolesArray as $role) {
            Role::factory()->create($role);
        }

        User::factory()->create([
            "name" => "Jayarith Rico",
            "email" => "rcjayarith@gmail.com",
            "role_id" => 1,
            "password" => bcrypt("password")
        ]);
        Provider::factory(5)->create();

        $brandsArray = [['name' => 'Diesel', 'description' => 'Diesel Description'], ['name' => 'Ayuza', 'description' => 'Ayuza Jeans']];
        foreach ($brandsArray as $brand) {
            Brand::factory()->create($brand);
        }

        $sizes = ["0", "1", "2", "3", "4", "6", "8", "10", "12", "14", "16", "XS", "S", "M", "L", "XL", "Unica"];
        foreach ($sizes as $size) {
            Size::factory()->create(["name" => $size]);
        }


        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
