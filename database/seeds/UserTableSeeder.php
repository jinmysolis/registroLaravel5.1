<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        factory(App\User::class)->create([
            'name' => 'jinmy',
            'email'=> 'solisjinmy@gmail.com',
            'role' => 'admin',
            'password' => bcrypt('137525627jinmy')
        ]);
        
        factory(App\User::class,49)->create();
        
    }
}
