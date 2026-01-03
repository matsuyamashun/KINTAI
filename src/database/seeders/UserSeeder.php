<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => '一般ユーザー',
            'email' => 'user100@example.com',
            'password' => Hash::make('password100'),
            'role' => User::ROLE_USER, //一般
        ]);
    }
}
