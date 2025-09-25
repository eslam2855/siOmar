<x-admin-layout>
    <div class="p-6">
        <!-- Welcome Section -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.welcome') }}, {{ Auth::user()->name }}!</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('admin.system_overview') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="text-right">
                        <p class="text-sm text-gray-500">{{ __('admin.today') }}</p>
                        <p class="text-lg font-semibold text-gray-900">{{ now()->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <!-- Total Reservations -->
            <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-blue-100 text-sm font-medium">{{ __('admin.total_reservations') }}</p>
                        <p class="text-3xl font-bold">{{ $stats['total_reservations'] }}</p>
                    </div>
                    <div class="bg-blue-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-calendar-check text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-blue-100 text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>12% {{ __('admin.from_last_month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Pending Reservations -->
            <div class="bg-gradient-to-br from-yellow-400 to-yellow-500 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-yellow-100 text-sm font-medium">{{ __('admin.pending_reservations') }}</p>
                        <p class="text-3xl font-bold">{{ $stats['pending_reservations'] }}</p>
                    </div>
                    <div class="bg-yellow-300 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-clock text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-yellow-100 text-sm">
                        <i class="fas fa-exclamation-triangle mr-1"></i>
                        <span>{{ __('admin.requires_attention') }}</span>
                    </div>
                </div>
            </div>

            <!-- Confirmed Reservations -->
            <div class="bg-gradient-to-br from-green-400 to-green-500 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-green-100 text-sm font-medium">{{ __('admin.confirmed_reservations') }}</p>
                        <p class="text-3xl font-bold">{{ $stats['confirmed_reservations'] }}</p>
                    </div>
                    <div class="bg-green-300 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-green-100 text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>8% {{ __('admin.from_last_month') }}</span>
                    </div>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-purple-100 text-sm font-medium">{{ __('admin.total_revenue') }}</p>
                        <p class="text-3xl font-bold">${{ number_format($stats['total_revenue'], 0) }}</p>
                    </div>
                    <div class="bg-purple-400 bg-opacity-30 rounded-full p-3">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="flex items-center text-purple-100 text-sm">
                        <i class="fas fa-arrow-up mr-1"></i>
                        <span>15% {{ __('admin.from_last_month') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2 mb-8">
            <!-- Recent Reservations -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.latest_reservations') }}</h3>
                        <a href="{{ route('admin.reservations') }}" class="text-indigo-600 hover:text-indigo-700 text-sm font-medium">
                            {{ __('admin.view_all') }}
                        </a>
                    </div>
                </div>
                <div class="p-6">
                    @if($recentReservations && $recentReservations->count() > 0)
                        <div class="space-y-4">
                            @foreach($recentReservations as $reservation)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                <i class="fas fa-user text-indigo-600"></i>
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $reservation->user->name }}</p>
                                            <p class="text-sm text-gray-500">{{ $reservation->unit->name ?? __('admin.unit') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                            @elseif($reservation->status === 'confirmed') bg-green-100 text-green-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ ucfirst(__('admin.' . $reservation->status)) }}
                                        </span>
                                        <p class="text-sm text-gray-500 mt-1">{{ $reservation->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <i class="fas fa-calendar-times text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">{{ __('admin.no_recent_activity') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.quick_stats') }}</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-home text-blue-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('admin.total_units') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ \App\Models\Unit::count() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-users text-green-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('admin.total_users') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">{{ \App\Models\User::count() }}</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-star text-purple-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('admin.average_rating') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">4.8</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="h-8 w-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                        <i class="fas fa-chart-line text-yellow-600 text-sm"></i>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-900">{{ __('admin.monthly_revenue') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-semibold text-gray-900">${{ number_format($stats['monthly_revenue'], 0) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 