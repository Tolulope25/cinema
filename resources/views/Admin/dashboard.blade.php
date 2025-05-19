@extends('layouts.app')

@section('content')
<div style="min-height: 100vh; display: flex;">
    <!-- Sidebar -->
    <div style="width: 250px; background-color: #1e293b; padding: 1.5rem;">
        <div style="color: white; font-size: 1.5rem; font-weight: 600; padding-bottom: 2rem; border-bottom: 1px solid #334155;">Cinema Admin</div>
        <nav style="margin-top: 2rem;">
            <a href="" style="display: block; color: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 6px; {{ request()->routeIs('admin.dashboard') ? 'background-color: #334155;' : '' }}">Dashboard</a>
            <a href="" style="display: block; color: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 6px; {{ request()->routeIs('admin.movies') ? 'background-color: #334155;' : '' }}">Movies</a>
            <a href="" style="display: block; color: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 6px; {{ request()->routeIs('admin.schedules') ? 'background-color: #334155;' : '' }}">Schedules</a>
            <a href="" style="display: block; color: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 6px; {{ request()->routeIs('admin.theatres') ? 'background-color: #334155;' : '' }}">Theatres</a>
            <a href="" style="display: block; color: white; padding: 0.75rem; margin-bottom: 0.5rem; border-radius: 6px; {{ request()->routeIs('admin.bookings') ? 'background-color: #334155;' : '' }}">Bookings</a>
        </nav>
    </div>

    <!-- Main Content -->
    <div style="flex: 1; padding: 2rem; background-color: #f1f5f9;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.875rem; font-weight: 600; color: #0f172a;">Dashboard</h1>
            <div style="display: flex; gap: 1rem;">
                {{-- <span style="font-weight: 500;">Welcome, {{ auth()->user()->name }}</span> --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" style="color: #dc2626;">Logout</button>
                </form>
            </div>
        </div>

        <!-- Stats Overview -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);">
                <h3 style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Total Movies</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #0f172a;">{{ $totalMovies }}</p>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);">
                <h3 style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Today Bookings</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #0f172a;">{{ $todayBookings }}</p>
            </div>
            <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);">
                <h3 style="font-size: 0.875rem; color: #64748b; margin-bottom: 0.5rem;">Total Revenue</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #0f172a;">₦{{ number_format($totalRevenue, 2) }}</p>
            </div>




            <table class="min-w-full border-collapse border border-gray-300 shadow-md rounded-lg overflow-hidden">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="px-4 py-2">Theatre id</th>
                        <th class="px-4 py-2">Theatre name</th>
                        <th class="px-4 py-2">Movie Tittle</th>
                        <th class="px-4 py-2">Show Date</th>
                        <th class="px-4 py-2">Show Time</th>

                        <th class="px-4 py-2">Capacity</th>
                        <th class="px-4 py-2">Seats Booked</th>
                        <th class="px-4 py-2">Seats Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($scheduleStats as $schedule)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $schedule['theatre_id'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $schedule['theatre_name'] }}</td>
                            <td class="px-4 py-3 font-semibold text-gray-800">{{ $schedule['movie_title'] }}</td>
                            <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($schedule['show_date'])->format('d M Y') }}</td>
                            <td class="px-4 py-3 text-center">{{ \Carbon\Carbon::parse($schedule['show_time'])->format('h:i A') }}</td>
                            <td class="px-4 py-3 text-center">{{ $schedule['theatre_capacity'] }} seats</td>
                            <td class="px-4 py-3 text-center text-green-600 font-bold">{{ $schedule['seats_booked'] }} seats</td>
                            <td class="px-4 py-3 text-center text-red-500 font-bold">{{ $schedule['seats_remaining'] }} seats</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-3 text-center text-gray-500">No schedules available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        <form action="{{ route('update.user') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex space-x-2">
                <select name="user_id" class="w-1/2" required>
                    <option value="">Select User</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">
                            {{ $user->name }} (ID: {{ $user->id }}) - Current Role: {{ $user->role }}
                        </option>
                    @endforeach
                </select>
                <select name="role" class="w-1/2" required>
                    <option value="">Select Role</option>
                    <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                </select>
            </div>
            <button type="submit" class="mt-2 btn btn-primary">Update Role</button>
        </form>

        <!-- Recent Bookings -->
        <div style="background: white; padding: 1.5rem; border-radius: 8px; box-shadow: 0 1px 3px rgb(0 0 0 / 0.1);">
            <h2 style="font-size: 1.25rem; font-weight: 600; color: #0f172a; margin-bottom: 1.5rem;">Recent Bookings</h2>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">ID</th>
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">Movie</th>
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">Customer</th>
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">Date</th>
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">Amount</th>
                        <th style="text-align: left; padding: 0.75rem; color: #64748b;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
<tr style="border-bottom: 1px solid #e2e8f0;">
    <td style="padding: 0.75rem;">#{{ $order->id }}</td>
    <td style="padding: 0.75rem;">
        @foreach($order->orderItems as $item)
            {{ $item->schedule->movie->title }}{{ !$loop->last ? ', ' : '' }}
        @endforeach
    </td>
    <td style="padding: 0.75rem;">{{ $order->email }}</td>
    <td style="padding: 0.75rem;">{{ $order->created_at->format('Y-m-d') }}</td>
    <td style="padding: 0.75rem;">₦{{ number_format($order->total_amount, 2) }}</td>
    <td style="padding: 0.75rem;">
        <span style="padding: 0.25rem 0.5rem; border-radius: 9999px; font-size: 0.75rem;
            {{ $order->status === 'paid' ? 'background-color: #dcfce7; color: #166534;' :
               ($order->status === 'pending' ? 'background-color: #fef3c7; color: #92400e;' :
               'background-color: #fee2e2; color: #991b1b;') }}">
            {{ ucfirst($order->status) }}
        </span>
    </td>
</tr>
@endforeach


</tbody>
</table>
</div>


<table class="min-w-full border-collapse border border-gray-300 shadow-md rounded-lg overflow-hidden">
    <thead class="bg-gray-800 text-white">
        <tr>
            <th class="px-4 py-2">Movie id</th>
        <th class="px-4 py-2">Movie name</th>
        <th class="px-4 py-2">Release date</th>
        <th class="px-4 py-2">End date</th>
        <th class="px-4 py-2">Edit Movie</th>
        <th class="px-4 py-2">Delete Movie</th>

</thead>
<tbody class="bg-white divide-y divide-gray-200">
    @forelse($movies as $movie)
    {{-- @foreach($movies as $movie)
    {{ dd($movie->release_date, $movie->end_date) }}
@endforeach --}}





    <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 font-semibold text-gray-800">{{ $movie->id }}</td>
            <td class="px-4 py-3 text-center">{{ $movie->title }}</td>
            <td class="px-4 py-3 font-semibold text-gray-800"> {{ \Carbon\Carbon::parse($movie->release_date)->format('d M Y') }} </td>


            <td class="px-4 py-3 text-center text-red-500 font-bold">  {{ $movie->end_date ? \Carbon\Carbon::parse($movie->end_date)->format('d M Y') : 'No End Date' }} </td>

            <td> <a href="{{ route('edit.movies',$movie->id) }}" class="btn btn-sm btn-outline-secondary">Edit</a></td>
            <td > <form action="{{ route('delete.movies', $movie->id) }}" method="POST" class="d-inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure you want to delete this Movie?')">Delete</button>
            </form>
        </td>
            @empty
        <tr>
            <td colspan="7" class="px-4 py-3 text-center text-gray-500">No Movies available.</td>
        </tr>
        @endforelse
    </tbody>
</table>

</div>
</div>

    @endsection
