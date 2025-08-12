<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $ticket->id }} - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
   
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.tickets.index') }}" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-arrow-left"></i> All Tickets
                    </a>
                    <h1 class="text-xl font-semibold text-gray-900">Ticket #{{ $ticket->id }}</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
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

    <main class="max-w-6xl mx-auto py-6 px-4">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif


        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                <ul>
                    @foreach($errors->all() as $error)
                        <li><i class="fas fa-exclamation-circle mr-2"></i>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            <div class="lg:col-span-2">

                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $ticket->subject }}</h2>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span><i class="fas fa-user mr-1"></i>{{ $ticket->user->name }}</span>
                                    <span><i class="fas fa-envelope mr-1"></i>{{ $ticket->user->email }}</span>
                                    <span><i class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                @if($ticket->status->value == 'open')
                                    <span class="px-3 py-1 text-sm bg-orange-100 text-orange-800 rounded-full">
                                        <i class="fas fa-clock mr-1"></i>Open
                                    </span>
                                @elseif($ticket->status->value == 'in_progress')
                                    <span class="px-3 py-1 text-sm bg-yellow-100 text-yellow-800 rounded-full">
                                        <i class="fas fa-spinner mr-1"></i>In Progress
                                    </span>
                                @elseif($ticket->status->value == 'closed')
                                    <span class="px-3 py-1 text-sm bg-green-100 text-green-800 rounded-full">
                                        <i class="fas fa-check-circle mr-1"></i>Closed
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="prose max-w-none">
                            <p class="text-gray-700 leading-relaxed">{{ $ticket->message }}</p>

                            @if($ticket->attachment)
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <i class="fas fa-paperclip text-gray-500 mr-2"></i>
                                        <span class="text-sm font-medium text-gray-700">Attachment:</span>
                                        <a href="{{ asset('storage/' . $ticket->attachment) }}"
                                           target="_blank"
                                           class="ml-2 text-blue-600 hover:text-blue-800 text-sm">
                                            View Attachment <i class="fas fa-external-link-alt ml-1"></i>
                                        </a>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>


                <div class="bg-white rounded-lg shadow">
                    <div class="p-6 border-b">
                        <h3 class="text-lg font-semibold text-gray-900">
                            <i class="fas fa-comments mr-2"></i>Messages
                            <span class="text-sm text-gray-500 font-normal">({{ $ticket->messages->count() }})</span>
                        </h3>
                    </div>

                    <div class="p-6">
                        @if($ticket->messages->count() > 0)
                            <div class="space-y-6">
                                @foreach($ticket->messages as $message)
                                    <div class="flex space-x-4">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gradient-to-r {{ $message->user->role->value === 'admin' ? 'from-purple-500 to-pink-500' : 'from-blue-500 to-green-500' }} rounded-full flex items-center justify-center">
                                                <span class="text-white font-medium text-sm">
                                                    {{ substr($message->user->name, 0, 1) }}
                                                </span>
                                            </div>
                                        </div>


                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h4 class="text-sm font-medium text-gray-900">{{ $message->user->name }}</h4>
                                                @if($message->user->role->value === 'admin')
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        <i class="fas fa-shield-alt mr-1"></i>Admin
                                                    </span>
                                                @endif
                                                <span class="text-xs text-gray-500">{{ $message->created_at->format('M j, Y g:i A') }}</span>
                                            </div>
                                            <div class="bg-gray-50 rounded-lg p-4">
                                                <p class="text-sm text-gray-700">{{ $message->message }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="fas fa-comment-slash text-4xl text-gray-300 mb-4"></i>
                                <p class="text-gray-500">No messages yet</p>
                            </div>
                        @endif


                        @if($ticket->status->value !== 'closed')
                            <div class="mt-8 border-t pt-6">
                                <form method="POST" action="{{ route('admin.tickets.reply', $ticket) }}">
                                    @csrf
                                    <div class="mb-4">
                                        <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-reply mr-1"></i>Reply to Ticket
                                        </label>
                                        <textarea
                                            id="message"
                                            name="message"
                                            rows="4"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                            placeholder="Type your reply here..."
                                            required
                                        >{{ old('message') }}</textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <i class="fas fa-paper-plane mr-2"></i>Send Reply
                                        </button>
                                    </div>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>


            <div class="lg:col-span-1">

                <div class="bg-white rounded-lg shadow p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-cogs mr-2"></i>Actions
                    </h3>


                    <form method="POST" action="{{ route('admin.tickets.update-status', $ticket) }}" class="mb-4">
                        @csrf
                        @method('PATCH')
                        <div class="mb-3">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Update Status
                            </label>
                            <select name="status" id="status" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="open" {{ $ticket->status->value === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $ticket->status->value === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="closed" {{ $ticket->status->value === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <button type="submit" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <i class="fas fa-save mr-2"></i>Update Status
                        </button>
                    </form>



                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">
                        <i class="fas fa-info-circle mr-2"></i>Ticket Information
                    </h3>
                    <div class="space-y-3">
                        <div>
                            <span class="text-sm font-medium text-gray-500">ID:</span>
                            <span class="text-sm text-gray-900 ml-2">#{{ $ticket->id }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Created:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                            <span class="text-sm text-gray-900 ml-2">{{ $ticket->updated_at->format('M j, Y g:i A') }}</span>
                        </div>
                        <div>
                            <span class="text-sm font-medium text-gray-500">Priority:</span>
                            @if($ticket->created_at->diffInHours() < 24)
                                <span class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">High</span>
                            @else
                                <span class="ml-2 px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">Normal</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>

        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(alert => {
                alert.style.transition = 'opacity 0.5s ease-out';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    </script>
</body>

</html>
