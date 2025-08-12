<?php

namespace Database\Seeders;

use App\Models\Ticket;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = json_decode(file_get_contents(base_path('database/data/Tickets.json')), true);


        foreach ($data['tickets'] as $ticket) {
            $ticket['created_at'] = now();
            $ticket['updated_at'] = now();
            Ticket::create($ticket);
        }
    }
}
