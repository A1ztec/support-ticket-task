<?php

namespace App\Http\Controllers\Admin;

use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Enums\Ticket\TicketStatus;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tickets' => Ticket::count(),
            'open_tickets' => Ticket::where('status', TicketStatus::OPEN)->count(),
            'in_progress_tickets' => Ticket::where('status', TicketStatus::IN_PROGRESS)->count(),
            'closed_tickets' => Ticket::where('status', TicketStatus::CLOSED)
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'new_tickets' => Ticket::where('status', TicketStatus::OPEN)
                ->whereDate('created_at', today())
                ->count(),
        ];


        $recent_tickets = Ticket::with(['user'])
            ->latest()
            ->limit(10)
            ->get();


        $chart_data = [
            'daily_tickets' => $this->getDailyTicketCounts()
        ];

        return view('admin.dashboard', compact('stats', 'recent_tickets', 'chart_data'));
    }

    private function getDailyTicketCounts(): array
    {
        $counts = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $count = Ticket::whereDate('created_at', $date)->count();
            $counts[] = $count;
        }

        return $counts;
    }
}
