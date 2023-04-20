<?php

namespace Database\Seeders;

use Database\Factories\UserFactory;
use Hash;

class UserSeeder extends BaseSeeder
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

        $users = [];
        for ($i = 0; $i < 20; $i++) {
            $definition = UserFactory::new()->regular()->definition();
            $definition['password'] = Hash::make('userpassword');
            $users[] = $definition;
        }
        $this->syncToDb($users);
    }
}
