<?php

namespace Database\Seeders;

use App\Models\GroupSize;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GroupSizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $groups = ["Pantalones/Jeans Caballero", "Pantalones/Jeans Dama", "Camisetas/Camisas Caballero o Dama", "Pantalones/Jeans Nino/Nina", "Camisas/Camisetas/Blusas Nino/Nina"];
        foreach ($groups as $group) {
            GroupSize::factory()->create(["name" => $group]);
        }
    }
}
