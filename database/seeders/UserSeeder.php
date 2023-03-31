<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Hash;
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
            'password' => Hash::make('admin'),
            'is_admin' => true,
        ]);

        UserFactory::new()->regular()->count(20)->create();
    }
}
