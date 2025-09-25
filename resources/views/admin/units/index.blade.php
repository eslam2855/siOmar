<x-admin-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.units_management') }}</h1>
                    <p class="mt-2 text-sm text-gray-600">{{ __('admin.manage_property_units') }}</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('admin.units.create') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('admin.add_new') }} {{ __('admin.units') }}
                    </a>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.filters') }}</h3>
            </div>
            <div class="p-6">
                <form method="GET" action="{{ route('admin.units') }}" class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label for="unit_type" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-building mr-2 text-indigo-500"></i>{{ __('admin.unit_type') }}
                        </label>
                        <select id="unit_type" name="unit_type" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            <option value="">{{ __('admin.all_types') }}</option>
                            @foreach($unitTypes ?? [] as $unitType)
                            <option value="{{ $unitType->id }}" {{ request('unit_type') == $unitType->id ? 'selected' : '' }}>
                                {{ $unitType->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2 text-indigo-500"></i>{{ __('admin.status') }}
                        </label>
                        <select id="status" name="status" class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            <option value="">{{ __('admin.all_statuses') }}</option>
                            <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>{{ __('admin.available') }}</option>
                            <option value="occupied" {{ request('status') == 'occupied' ? 'selected' : '' }}>{{ __('admin.occupied') }}</option>
                            <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>{{ __('admin.maintenance') }}</option>
                            <option value="reserved" {{ request('status') == 'reserved' ? 'selected' : '' }}>{{ __('admin.reserved') }}</option>
                        </select>
                    </div>
                    <div>
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-search mr-2 text-indigo-500"></i>{{ __('admin.search') }}
                        </label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" 
                               placeholder="{{ __('admin.search_placeholder') }}" 
                               class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 shadow-sm hover:shadow-md">
                            <i class="fas fa-filter mr-2"></i>{{ __('admin.filter') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Units Grid -->
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @forelse($units as $unit)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-lg transition-all duration-200 overflow-hidden">
                <!-- Unit Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg">
                                    <i class="fas fa-home text-white text-lg"></i>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">
                                    {{ $unit->name }}
                                </h3>
                                <p class="text-sm text-gray-500">
                                    {{ $unit->unit_number }}
                                </p>
                            </div>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if($unit->status === 'available') bg-green-100 text-green-800
                            @elseif($unit->status === 'occupied') bg-red-100 text-red-800
                            @elseif($unit->status === 'maintenance') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            <i class="fas fa-circle mr-1.5 text-xs"></i>
                            {{ __('admin.' . $unit->status) }}
                        </span>
                    </div>
                </div>

                <!-- Unit Image -->
                @if($unit->primaryImage)
                <div class="relative h-48 bg-gray-200">
                    <img src="{{ asset('storage/' . $unit->primaryImage->image_path) }}" 
                         alt="{{ $unit->name }}"
                         class="w-full h-full object-cover">
                </div>
                @else
                <div class="relative h-48 bg-gray-200 flex items-center justify-center">
                    <div class="text-center">
                        <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                                        <p class="text-sm text-gray-500">{{ __('admin.no_image_available') }}</p>
                    </div>
                </div>
                @endif

                <!-- Unit Details -->
                <div class="p-6">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                    <i class="fas fa-building text-blue-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.unit_type') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $unit->unitType->name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                                    <i class="fas fa-users text-green-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.max_guests') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $unit->max_guests }} guests</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                    <i class="fas fa-bed text-purple-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.bedrooms') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $unit->bedrooms }}</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-pink-100 flex items-center justify-center">
                                    <i class="fas fa-bath text-pink-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.bathrooms') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $unit->bathrooms }}</p>
                            </div>
                        </div>
                        @if($unit->size_sqm)
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-yellow-100 flex items-center justify-center">
                                    <i class="fas fa-ruler-combined text-yellow-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.size_sqm') }}</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $unit->size_sqm }} sqm</p>
                            </div>
                        </div>
                        @endif
                        @php
                            $now = now();
                            $ym = $now->format('Y-m');
                            $currentMonthPrice = optional($unit->monthPrices->firstWhere('year_month', $ym))->daily_price;
                        @endphp
                        <div class="flex items-center space-x-2">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-dollar-sign text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-xs font-medium text-gray-500">{{ __('admin.daily_price_this_month') }}</p>
                                <p class="text-sm font-semibold text-gray-900">
                                    @if($currentMonthPrice)
                                        {{ number_format($currentMonthPrice, 0) }} EGP/night
                                    @else
                                        <span class="text-gray-400">Not set</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                </div>

                <!-- Actions -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.units.show', $unit) }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            {{ __('admin.view') }} {{ __('admin.actions') }}
                        </a>
                        <a href="{{ route('admin.units.edit', $unit) }}" 
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-gray-600 to-gray-700 hover:from-gray-700 hover:to-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-all duration-200">
                            <i class="fas fa-edit mr-2"></i>
                            {{ __('admin.edit') }}
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full">
                <div class="text-center py-12">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                        <i class="fas fa-home text-3xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('admin.no_units_found') }}</h3>
                    <p class="text-gray-500 mb-6">{{ __('admin.get_started_create_unit') }}</p>
                    <a href="{{ route('admin.units.create') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>
                        {{ __('admin.add_new') }} {{ __('admin.units') }}
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($units->hasPages())
        <div class="mt-8">
            {{ $units->links() }}
        </div>
        @endif
    </div>
</x-admin-layout> 