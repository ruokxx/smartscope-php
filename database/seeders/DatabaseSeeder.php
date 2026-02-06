<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ScopesSeeder::class,
            ObjectsSeeder::class,
            DemoUserSeeder::class,
	    AdminUserSeeder::class,
        ]);
    }
}
