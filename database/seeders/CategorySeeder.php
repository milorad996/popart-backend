<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $electronics = Category::create([
            'name' => 'Elektronika',
            'slug' => 'elektronika',
            'parent_id' => null
        ]);

        $vehicles = Category::create([
            'name' => 'Vozila',
            'slug' => 'vozila',
            'parent_id' => null
        ]);

        $realEstate = Category::create([
            'name' => 'Nekretnine',
            'slug' => 'nekretnine',
            'parent_id' => null
        ]);

        Category::create([
            'name' => 'Telefoni',
            'slug' => 'telefoni',
            'parent_id' => $electronics->id
        ]);

        Category::create([
            'name' => 'Laptopovi',
            'slug' => 'laptopovi',
            'parent_id' => $electronics->id
        ]);

        Category::create([
            'name' => 'TV i Audio',
            'slug' => 'tv-audio',
            'parent_id' => $electronics->id
        ]);

        Category::create([
            'name' => 'Automobili',
            'slug' => 'automobili',
            'parent_id' => $vehicles->id
        ]);

        Category::create([
            'name' => 'Motori',
            'slug' => 'motori',
            'parent_id' => $vehicles->id
        ]);

        Category::create([
            'name' => 'Stanovi',
            'slug' => 'stanovi',
            'parent_id' => $realEstate->id
        ]);

        Category::create([
            'name' => 'Kuće',
            'slug' => 'kuce',
            'parent_id' => $realEstate->id
        ]);
    }
}
