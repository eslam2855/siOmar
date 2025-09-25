<x-admin-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-calendar-check text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.reservation_details') }}</h1>
                            <p class="text-gray-600 mt-1">{{ __('admin.booking_information') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.reservations') }}" class="flex items-center space-x-2 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200">
                            <i class="fas fa-arrow-left text-gray-300"></i>
                            <span>{{ __('admin.back') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
            <div class="mb-6">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-check text-green-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(session('error'))
            <div class="mb-6">
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-xl p-4 shadow-sm">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-sm"></i>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column - Reservation Details -->
                <div class="lg:col-span-2 space-y-6">
                    
                    <!-- Reservation Overview Card -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 px-6 py-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <i class="fas fa-hashtag text-white text-xl"></i>
                                    <div>
                                        <h2 class="text-xl font-bold text-white">{{ __('admin.reservation_number') }} #{{ $reservation->reservation_number }}</h2>
                                        <p class="text-blue-100 text-sm">{{ __('admin.created_at') }}: {{ $reservation->created_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($reservation->status === 'confirmed') bg-green-100 text-green-800
                                        @elseif($reservation->status === 'active') bg-blue-100 text-blue-800
                                        @elseif($reservation->status === 'completed') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        <i class="fas fa-circle text-xs mr-2"></i>
                                        {{ __("admin.status_{$reservation->status}") }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Guest Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-user text-blue-500 mr-2"></i>
                                        {{ __('admin.guest_information') }}
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.guest_name') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->user->name }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.guest_email') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->user->email }}</span>
                                        </div>
                                        @if($reservation->user->phone_number)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.guest_phone') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->user->phone_number }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Booking Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                        {{ __('admin.booking_information') }}
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.unit') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->unit->name }} ({{ $reservation->unit->unitType->name }})</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.check_in_date') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->check_in_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.check_out_date') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->check_out_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.number_of_nights') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->check_in_date->diffInDays($reservation->check_out_date) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.number_of_guests') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->number_of_guests }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Card -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-credit-card text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.payment_information') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Pricing Details -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-tags text-green-500 mr-2"></i>
                                        {{ __('admin.pricing_details') }}
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.total_amount') }}:</span>
                                            <span class="text-lg font-bold text-green-600">{{ __('admin.currency_symbol') }}{{ number_format($reservation->total_amount, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.cleaning_fee') }}:</span>
                                            <span class="text-sm text-gray-900">{{ __('admin.currency_symbol') }}{{ number_format($reservation->cleaning_fee, 2) }}</span>
                                        </div>
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.security_deposit') }}:</span>
                                            <span class="text-sm text-gray-900">{{ __('admin.currency_symbol') }}{{ number_format($reservation->security_deposit, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Deposit Information -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-piggy-bank text-yellow-500 mr-2"></i>
                                        {{ __('admin.deposit_management') }}
                                    </h3>
                                    <div class="space-y-3">
                                        @if($reservation->minimum_deposit_amount || $reservation->deposit_percentage)
                                            @if($reservation->minimum_deposit_amount)
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm font-medium text-gray-600">{{ __('admin.minimum_deposit_amount') }}:</span>
                                                <span class="text-sm text-gray-900">{{ __('admin.currency_symbol') }}{{ number_format($reservation->minimum_deposit_amount, 2) }}</span>
                                            </div>
                                            @endif
                                            @if($reservation->deposit_percentage)
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm font-medium text-gray-600">{{ __('admin.deposit_percentage') }}:</span>
                                                <span class="text-sm text-gray-900">{{ $reservation->deposit_percentage }}%</span>
                                            </div>
                                            @if($reservation->calculateDepositAmount())
                                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                                <span class="text-sm font-medium text-gray-600">{{ __('admin.calculated_deposit_amount') }}:</span>
                                                <span class="text-sm font-bold text-yellow-600">{{ __('admin.currency_symbol') }}{{ number_format($reservation->calculateDepositAmount(), 2) }}</span>
                                            </div>
                                            @endif
                                            @endif
                                        @else
                                            <div class="text-sm text-gray-500 italic">{{ __('admin.no_deposit_requirements') }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Information Card -->
                    @if($reservation->transfer_amount || $reservation->transfer_image)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-blue-500 to-cyan-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-exchange-alt text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.transfer_management') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Transfer Details -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-money-bill-wave text-blue-500 mr-2"></i>
                                        {{ __('admin.transfer_details') }}
                                    </h3>
                                    <div class="space-y-3">
                                        @if($reservation->transfer_amount)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.transfer_amount') }}:</span>
                                            <span class="text-lg font-bold text-blue-600">{{ __('admin.currency_symbol') }}{{ number_format($reservation->transfer_amount, 2) }}</span>
                                        </div>
                                        @endif
                                        @if($reservation->transfer_date)
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.transfer_date') }}:</span>
                                            <span class="text-sm text-gray-900">{{ $reservation->transfer_date->format('M d, Y') }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Transfer Receipt -->
                                @if($reservation->transfer_image)
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-receipt text-blue-500 mr-2"></i>
                                        {{ __('admin.transfer_receipt') }}
                                    </h3>
                                    <div class="space-y-3">
                                        <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                            <span class="text-sm font-medium text-gray-600">{{ __('admin.receipt_image') }}:</span>
                                            <a href="{{ asset('storage/' . $reservation->transfer_image) }}" target="_blank" class="text-blue-600 hover:text-blue-800 underline text-sm">
                                                <i class="fas fa-external-link-alt mr-1"></i>
                                                {{ __('admin.view_receipt') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Special Requests & Notes -->
                    @if($reservation->special_requests || $reservation->admin_notes)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-purple-500 to-pink-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-sticky-note text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.notes_and_requests') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                @if($reservation->special_requests)
                                <!-- Special Requests -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-star text-purple-500 mr-2"></i>
                                        {{ __('admin.special_requests') }}
                                    </h3>
                                    <div class="bg-purple-50 border border-purple-200 rounded-xl p-4">
                                        <p class="text-sm text-purple-900">{{ $reservation->special_requests }}</p>
                                    </div>
                                </div>
                                @endif

                                @if($reservation->admin_notes)
                                <!-- Admin Notes -->
                                <div class="space-y-4">
                                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                                        <i class="fas fa-user-shield text-pink-500 mr-2"></i>
                                        {{ __('admin.admin_notes') }}
                                    </h3>
                                    <div class="bg-pink-50 border border-pink-200 rounded-xl p-4">
                                        <p class="text-sm text-pink-900">{{ $reservation->admin_notes }}</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Cancellation Information -->
                    @if($reservation->cancellation_reason)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-times-circle text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.cancellation_information') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-red-50 border border-red-200 rounded-xl p-4">
                                <h3 class="text-lg font-semibold text-red-900 mb-2">{{ __('admin.cancellation_reason') }}</h3>
                                <p class="text-sm text-red-800">{{ $reservation->cancellation_reason }}</p>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <!-- Right Column - Actions & Management -->
                <div class="space-y-6">
                    
                    <!-- Status Actions Card -->
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-cogs text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.status_actions') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6 space-y-4">
                            @if($reservation->status === 'pending')
                            <div class="space-y-3">
                                @if($reservation->deposit_verified)
                                <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all duration-200">
                                        <i class="fas fa-check text-white"></i>
                                        <span>{{ __('admin.confirm') }}</span>
                                    </button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.reservations.approve', $reservation) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all duration-200">
                                        <i class="fas fa-check text-white"></i>
                                        <span>{{ __('admin.approve') }}</span>
                                    </button>
                                </form>
                                @endif
                                <button onclick="openRejectModal()" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200">
                                    <i class="fas fa-times text-white"></i>
                                    <span>{{ __('admin.reject') }}</span>
                                </button>
                            </div>
                            @endif

                            @if($reservation->status === 'confirmed')
                            <div class="space-y-3">
                                @if($reservation->canBeActivated())
                                <form method="POST" action="{{ route('admin.reservations.activate', $reservation) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-all duration-200">
                                        <i class="fas fa-play text-white"></i>
                                        <span>{{ __('admin.activate') }}</span>
                                    </button>
                                </form>
                                @else
                                <div class="text-sm text-gray-500 text-center py-2">
                                    {{ __('admin.cannot_activate_before_checkin') }}
                                </div>
                                @endif
                                <button onclick="openCancelModal()" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200">
                                    <i class="fas fa-ban text-white"></i>
                                    <span>{{ __('admin.cancel') }}</span>
                                </button>
                            </div>
                            @endif

                            @if($reservation->status === 'active')
                            <div class="space-y-3">
                                @if($reservation->canBeCompleted())
                                <form method="POST" action="{{ route('admin.reservations.complete', $reservation) }}" class="w-full">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-gray-500 text-white rounded-xl hover:bg-gray-600 transition-all duration-200">
                                        <i class="fas fa-flag-checkered text-white"></i>
                                        <span>{{ __('admin.complete') }}</span>
                                    </button>
                                </form>
                                @else
                                <div class="text-sm text-gray-500 text-center py-2">
                                    {{ __('admin.cannot_complete_before_checkout') }}
                                </div>
                                @endif
                            </div>
                            @endif

                            @if(in_array($reservation->status, ['pending', 'confirmed']))
                            <div class="space-y-3">
                                <button onclick="openCancelModal()" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200">
                                    <i class="fas fa-ban text-white"></i>
                                    <span>{{ __('admin.cancel') }}</span>
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Deposit Verification Card -->
                    @if($reservation->transfer_amount && !$reservation->deposit_verified)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-clock text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.deposit_verification') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-exclamation-triangle text-yellow-600 mt-1"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-yellow-800">{{ __('admin.deposit_pending_verification') }}</h3>
                                        <div class="mt-2 text-sm text-yellow-700 space-y-1">
                                            <p>{{ __('admin.transfer_amount') }}: {{ __('admin.currency_symbol') }}{{ number_format($reservation->transfer_amount, 2) }}</p>
                                            <p>{{ __('admin.required_amount') }}: {{ __('admin.currency_symbol') }}{{ number_format($reservation->getDepositAmount() ?? 0, 2) }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('admin.reservations.verify-deposit', $reservation) }}" class="w-full">
                                @csrf
                                <button type="submit" class="w-full flex items-center justify-center space-x-2 px-4 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all duration-200">
                                    <i class="fas fa-check-circle text-white"></i>
                                    <span>{{ __('admin.verify_deposit') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                    @if($reservation->deposit_verified)
                    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle text-white text-xl mr-3"></i>
                                <h2 class="text-xl font-bold text-white">{{ __('admin.deposit_status') }}</h2>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                                <div class="flex items-start space-x-3">
                                    <i class="fas fa-check-circle text-green-600 mt-1"></i>
                                    <div>
                                        <h3 class="text-sm font-medium text-green-800">{{ __('admin.deposit_verified') }}</h3>
                                        <p class="text-sm text-green-700 mt-1">{{ __('admin.verified_on') }}: {{ $reservation->deposit_verified_at->format('M d, Y \a\t g:i A') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Management Forms Section -->
            <div class="mt-8 space-y-6">
                
                <!-- Admin Notes & Deposit Settings -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-edit text-white text-xl mr-3"></i>
                            <h2 class="text-xl font-bold text-white">{{ __('admin.admin_notes_deposit_settings') }}</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.reservations.admin-notes', $reservation) }}" class="space-y-6">
                            @csrf
                            <div>
                                <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.admin_notes') }}</label>
                                <textarea id="admin_notes" name="admin_notes" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="{{ __('admin.add_notes_for_guest') }}">{{ $reservation->admin_notes }}</textarea>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="minimum_deposit_amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.minimum_deposit_amount') }} ({{ __('admin.currency_symbol') }})</label>
                                    <input type="number" step="0.01" id="minimum_deposit_amount" name="minimum_deposit_amount" value="{{ $reservation->minimum_deposit_amount }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                                </div>
                                <div>
                                    <label for="deposit_percentage" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.deposit_percentage') }} (%)</label>
                                    <input type="number" step="0.01" min="0" max="100" id="deposit_percentage" name="deposit_percentage" value="{{ $reservation->deposit_percentage }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200" placeholder="0">
                                </div>
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="flex items-center space-x-2 px-6 py-3 bg-indigo-500 text-white rounded-xl hover:bg-indigo-600 transition-all duration-200">
                                    <i class="fas fa-save text-white"></i>
                                    <span>{{ __('admin.update_notes_deposit_settings') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Transfer Details Management -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-exchange-alt text-white text-xl mr-3"></i>
                            <h2 class="text-xl font-bold text-white">{{ __('admin.transfer_details_management') }}</h2>
                        </div>
                    </div>
                    
                    <div class="p-6">
                        <form method="POST" action="{{ route('admin.reservations.transfer-details', $reservation) }}" enctype="multipart/form-data" class="space-y-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="transfer_amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.transfer_amount') }} ({{ __('admin.currency_symbol') }})</label>
                                    <input type="number" step="0.01" id="transfer_amount" name="transfer_amount" value="{{ $reservation->transfer_amount }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                                </div>
                                <div>
                                    <label for="transfer_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.transfer_date') }}</label>
                                    <input type="date" id="transfer_date" name="transfer_date" value="{{ $reservation->transfer_date ? $reservation->transfer_date->format('Y-m-d') : '' }}" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                </div>
                            </div>
                            <div>
                                <label for="transfer_image" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.transfer_receipt_image') }}</label>
                                <input type="file" id="transfer_image" name="transfer_image" accept="image/*" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200">
                                @if($reservation->transfer_image)
                                    <p class="mt-2 text-sm text-gray-500">{{ __('admin.current') }}: <a href="{{ asset('storage/' . $reservation->transfer_image) }}" target="_blank" class="text-green-600 hover:text-green-800 underline">{{ __('admin.view_current_receipt') }}</a></p>
                                @endif
                            </div>
                            <div class="flex justify-end">
                                <button type="submit" class="flex items-center space-x-2 px-6 py-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition-all duration-200">
                                    <i class="fas fa-save text-white"></i>
                                    <span>{{ __('admin.update_transfer_details') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-times text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.reject_reservation') }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.reservations.reject', $reservation) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="rejection_reason" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.rejection_reason') }}</label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required placeholder="{{ __('admin.enter_rejection_reason') }}"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeRejectModal()" class="flex items-center space-x-2 px-4 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition-all duration-200">
                            <i class="fas fa-times text-gray-600"></i>
                            <span>{{ __('admin.cancel') }}</span>
                        </button>
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200">
                            <i class="fas fa-times text-white"></i>
                            <span>{{ __('admin.reject') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cancel Modal -->
    <div id="cancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-xl bg-white">
            <div class="mt-3">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-ban text-red-600"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.cancel_reservation') }}</h3>
                </div>
                <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}">
                    @csrf
                    <div class="mb-4">
                        <label for="cancellation_reason" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.cancellation_reason') }}</label>
                        <textarea id="cancellation_reason" name="cancellation_reason" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" required placeholder="{{ __('admin.enter_cancellation_reason') }}"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="refund_amount" class="block text-sm font-medium text-gray-700 mb-2">{{ __('admin.refund_amount') }} ({{ __('admin.currency_symbol') }})</label>
                        <input type="number" step="0.01" min="0" max="{{ $reservation->total_amount }}" id="refund_amount" name="refund_amount" value="0" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200" placeholder="0.00">
                        <p class="mt-1 text-sm text-gray-500">{{ __('admin.max_refund') }}: {{ __('admin.currency_symbol') }}{{ number_format($reservation->total_amount, 2) }}</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCancelModal()" class="flex items-center space-x-2 px-4 py-2 bg-gray-300 text-gray-800 rounded-xl hover:bg-gray-400 transition-all duration-200">
                            <i class="fas fa-times text-gray-600"></i>
                            <span>{{ __('admin.back') }}</span>
                        </button>
                        <button type="submit" class="flex items-center space-x-2 px-4 py-2 bg-red-500 text-white rounded-xl hover:bg-red-600 transition-all duration-200">
                            <i class="fas fa-ban text-white"></i>
                            <span>{{ __('admin.cancel_reservation') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openRejectModal() {
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('rejection_reason').value = '';
        }

        function openCancelModal() {
            document.getElementById('cancelModal').classList.remove('hidden');
        }

        function closeCancelModal() {
            document.getElementById('cancelModal').classList.add('hidden');
            document.getElementById('cancellation_reason').value = '';
            document.getElementById('refund_amount').value = '0';
        }
    </script>
</x-admin-layout> 