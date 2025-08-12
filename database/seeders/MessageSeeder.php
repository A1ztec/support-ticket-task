<?php

namespace Database\Seeders;

use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('database/data/Messages.json')), true);

        foreach ($data['messages'] as $message) {
            $message['created_at'] = now();
            $message['updated_at'] = now();
            Message::create($message);
        }
    }
}
