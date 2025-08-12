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
    <!-- Header -->
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

    <main class="max-w-4xl mx-auto py-6 px-4">
        <!-- Success Messages -->
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        <!-- Error Messages -->
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
            <!-- Main Ticket Content -->
            <div class="lg:col-span-2">
                <!-- Ticket Details -->
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="p-6 border-b">
                        <div class="flex justify-between items-start">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $ticket->subject }}</h2>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <span><i class="fas fa-user mr-1"></i>{{ $ticket->user->name }}</span>
                                    <span><i
                                            class="fas fa-calendar mr-1"></i>{{ $ticket->created_at->format('M j, Y g:i A') }}</span>
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
                            <p class="text-gray-700
