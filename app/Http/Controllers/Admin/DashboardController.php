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
            'open_tickets' => Ticket::open()->count(),
            'in_progress_tickets' => Ticket::inProgress()->count(),
            'closed_tickets' => Ticket::closed()
                ->whereMonth('updated_at', now()->month)
                ->count(),
            'new_tickets' => Ticket::open()
                ->whereDate('created_at', today())
                ->count(),
        ];


        $recent_tickets = Ticket::with(['user'])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recent_tickets'));
    }
}
