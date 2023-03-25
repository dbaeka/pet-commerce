<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        UserFactory::new()->create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@buckhill.co.uk',
            'password' => 'admin',
            'is_admin' => true,
        ]);
    }
}
