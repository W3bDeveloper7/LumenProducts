<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');

        \App\User::create([
            'name' => 'Ahmed Adel',
            'type' => 1,
            'email' => 'ahmed@domain.com',
            'password' => 123456,
        ]);

        \App\User::create([
            'name' => 'test Account',
            'type' => 2,
            'email' => 'viewer@domain.com',
            'password' => 123456,
        ]);

        factory(App\Product::class, 10)->create();

    }
}
