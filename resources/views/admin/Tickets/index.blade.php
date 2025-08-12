<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Tickets - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i> Dashboard
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">All Tickets</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto py-6 px-4">
        <!-- Filters -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <form method="GET" action="{{ route('admin.tickets.index') }}" class="flex flex-wrap gap-4 items-end">
                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select name="status" class="border border-gray-300 rounded-md px-3 py-2">
                        <option value="">All Statuses</option>
                        <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress
                        </option>
                        <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                    </select>
                </div>

                <!-- Search -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search tickets..."
                        class="border border-gray-300 rounded-md px-3 py-2">
                </div>

                <!-- Buttons -->
                <div>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-search mr-1"></i> Filter
                    </button>
                    <a href="{{ route('admin.tickets.index') }}"
                        class="ml-2 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                        Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Tickets Table -->
        <div class="bg-white rounded-lg shadow">
            <div class="p-6 border-b">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold">
                        Tickets ({{ $tickets->total() ?? 0 }})
                    </h2>
                </div>
            </div>

            @if($tickets->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($tickets as $ticket)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm font-medium text-gray-900">#{{ $ticket->id }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($ticket->subject, 40) }}
                                        </div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($ticket->message, 60) }}</div>
                                        @if($ticket->attachment)
                                            <div class="mt-1">
                                                <i class="fas fa-paperclip text-gray-400 text-xs"></i>
                                                <span class="text-xs text-gray-500">Has attachment</span>
                                            </div>
                                        @endif
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
                                    <td class="px-6 py-4">
                                        @if($ticket->created_at->diffInHours() < 24)
                                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">High</span>
                                        @else
                                            <span class="px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Normal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <div>{{ $ticket->created_at->format('M j, Y') }}</div>
                                        <div class="text-xs">{{ $ticket->created_at->format('g:i A') }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex space-x-2">
                                            <a href="{{ route('admin.tickets.show', $ticket) }}"
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </a>
                                            @if($ticket->status->value != 'closed')
                                                <form method="POST" action="{{ route('admin.tickets.close', $ticket) }}"
                                                    class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="bg-green-600 text-white px-3 py-1 rounded text-xs hover:bg-green-700"
                                                        onclick="return confirm('Close this ticket?')">
                                                        <i class="fas fa-times mr-1"></i>Close
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($tickets->hasPages())
                    <div class="p-6 border-t">
                        {{ $tickets->links() }}
                    </div>
                @endif
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-inbox text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 text-lg">No tickets found</p>
                    <p class="text-gray-400 text-sm">Try adjusting your filters</p>
                </div>
            @endif
        </div>
    </main>
</body>

</html>
