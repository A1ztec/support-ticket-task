<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Simple Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-900">Admin Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">Welcome, {{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.auth.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto py-6 px-4">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Total Tickets -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <i class="fas fa-ticket-alt text-blue-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Total Tickets</p>
                        <p class="text-2xl font-bold">{{ $stats['total_tickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Open Tickets -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-orange-100 rounded-lg">
                        <i class="fas fa-clock text-orange-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Open Tickets</p>
                        <p class="text-2xl font-bold text-orange-600">{{ $stats['open_tickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- In Progress -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <i class="fas fa-spinner text-yellow-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">In Progress</p>
                        <p class="text-2xl font-bold text-yellow-600">{{ $stats['in_progress_tickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Closed -->
            <div class="bg-white p-6 rounded-lg shadow">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm text-gray-600">Closed This Month</p>
                        <p class="text-2xl font-bold text-green-600">{{ $stats['closed_tickets'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="bg-white p-6 rounded-lg shadow mb-8">
            <h2 class="text-lg font-semibold mb-4">Quick Actions</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <a href="{{ route('admin.tickets.index') }}"
                    class="bg-blue-600 text-white p-4 rounded-lg hover:bg-blue-700 text-center transition duration-200">
                    <i class="fas fa-list mb-2 block text-2xl"></i>
                    <div class="font-semibold">All Tickets</div>
                    <div class="text-sm opacity-80">View & manage all tickets</div>
                </a>
                <a href="{{ route('admin.tickets.index', ['status' => 'open']) }}"
                    class="bg-orange-600 text-white p-4 rounded-lg hover:bg-orange-700 text-center transition duration-200">
                    <i class="fas fa-exclamation-circle mb-2 block text-2xl"></i>
                    <div class="font-semibold">Open Tickets</div>
                    <div class="text-sm opacity-80">{{ $stats['open_tickets'] ?? 0 }} tickets need attention</div>
                </a>
                <a href="{{ route('admin.tickets.index', ['status' => 'in_progress']) }}"
                    class="bg-yellow-600 text-white p-4 rounded-lg hover:bg-yellow-700 text-center transition duration-200">
                    <i class="fas fa-clock mb-2 block text-2xl"></i>
                    <div class="font-semibold">In Progress</div>
                    <div class="text-sm opacity-80">{{ $stats['in_progress_tickets'] ?? 0 }} active tickets</div>
                </a>
                <a href="{{ route('admin.tickets.index', ['status' => 'closed']) }}"
                    class="bg-green-600 text-white p-4 rounded-lg hover:bg-green-700 text-center transition duration-200">
                    <i class="fas fa-check-circle mb-2 block text-2xl"></i>
                    <div class="font-semibold">Closed Tickets</div>
                    <div class="text-sm opacity-80">View resolved tickets</div>
                </a>
            </div>
        </div>


        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">Recent Tickets</h2>
                    <a href="{{ route('admin.tickets.index') }}" class="text-blue-600 hover:text-blue-800 text-sm">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
            </div>

            @if(isset($recent_tickets) && $recent_tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($recent_tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $ticket->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->subject, 40) }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($ticket->message, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $ticket->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $ticket->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($ticket->status->value == 'open')
                                            <span class="px-2 py-1 text-xs bg-orange-100 text-orange-800 rounded-full">
                                                <i class="fas fa-clock mr-1"></i>Open
                                            </span>
                                        @elseif($ticket->status->value == 'in_progress')
                                            <span class="px-2 py-1 text-xs bg-yellow-100 text-yellow-800 rounded-full">
                                                <i class="fas fa-spinner mr-1"></i>In Progress
                                            </span>
                                        @elseif($ticket->status->value == 'closed')
                                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">
                                                <i class="fas fa-check-circle mr-1"></i>Closed
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div>{{ $ticket->created_at->format('M j, Y') }}</div>
                                        <div class="text-xs">{{ $ticket->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('admin.tickets.show', $ticket) }}"
                                           class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                            <i class="fas fa-eye mr-1"></i>View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No tickets yet</p>
                    <p class="text-gray-400 text-sm">Tickets will appear here when users create them</p>
                </div>
            @endif
        </div>
    </main>
</body>

</html>
