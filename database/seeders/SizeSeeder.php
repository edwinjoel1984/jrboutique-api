<?php

namespace Database\Seeders;

use App\Models\Size;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sizes = ["0", "1", "2", "3", "4", "6", "8", "10", "12", "14", "16", "XS", "S", "M", "L", "XL", "Unica"];
        foreach ($sizes as $size) {
            Size::factory()->create(["name" => $size]);
        }
    }
}
