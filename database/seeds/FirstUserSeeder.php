<?php

use App\User;
use Illuminate\Database\Seeder;

class FirstUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      User::create([
        'firstname' => 'Admin',
        'lastname' => 'Remove me please !',
        'email' => 'admin@example.com',
        'password' => bcrypt('admin'),
        'is_admin' => 1,
      ]);
    }
}
