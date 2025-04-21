<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Author;
use App\Models\Book;
use App\Models\Category;
use App\Models\Publisher;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Admin::factory(1)->create([
        //     'username' => 'admin',
        //     'password' => bcrypt('password'),
        // ]);

        User::factory(1)->create([
            'email' => 'test1@gmail.com',
            'password' => bcrypt('password'),
        ]);

        // Category::factory(10)->create();
        // Publisher::factory(10)->create();
        // Author::factory(10)->create();
        // Book::factory(10)->create([
        //     'category_id' => Category::all()->random()->id,
        //     'publisher_id' => Publisher::all()->random()->id,
        //     'author_id' => Author::all()->random()->id,
        // ]);
    }
}
