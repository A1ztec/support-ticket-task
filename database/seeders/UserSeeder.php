<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('database/data/Users.json')), true);

        foreach ($data['users'] as $user) {
            $user['password'] = bcrypt('password');
            $user['email_verified_at'] = now();
            User::create($user);
        }
    }
}
