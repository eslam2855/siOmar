<x-admin-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.units.show', $unit) }}" class="text-indigo-600 hover:text-indigo-700">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Edit Unit</h1>
                            <p class="text-sm text-gray-600">{{ $unit->name }} ({{ $unit->unit_number }})</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Unit Information</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('admin.units.update', $unit) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <!-- Unit Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-home mr-2 text-indigo-500"></i>Unit Name
                            </label>
                            <input type="text" id="name" name="name" value="{{ old('name', $unit->name) }}" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Number -->
                        <div>
                            <label for="unit_number" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-hashtag mr-2 text-indigo-500"></i>Unit Number
                            </label>
                            <input type="text" id="unit_number" name="unit_number" value="{{ old('unit_number', $unit->unit_number) }}" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('unit_number')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Unit Type -->
                        <div>
                            <label for="unit_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-building mr-2 text-indigo-500"></i>Unit Type
                            </label>
                            <select id="unit_type_id" name="unit_type_id" required 
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                <option value="">Select Unit Type</option>
                                @foreach($unitTypes as $unitType)
                                    <option value="{{ $unitType->id }}" {{ old('unit_type_id', $unit->unit_type_id) == $unitType->id ? 'selected' : '' }}>
                                        {{ $unitType->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('unit_type_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-info-circle mr-2 text-indigo-500"></i>Status
                            </label>
                            <select id="status" name="status" required 
                                    class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                <option value="">Select Status</option>
                                <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ old('status', $unit->status) == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ old('status', $unit->status) == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                                <option value="reserved" {{ old('status', $unit->status) == 'reserved' ? 'selected' : '' }}>Reserved</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bedrooms -->
                        <div>
                            <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-bed mr-2 text-indigo-500"></i>Bedrooms
                            </label>
                            <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', $unit->bedrooms) }}" min="1" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('bedrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Bathrooms -->
                        <div>
                            <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-bath mr-2 text-indigo-500"></i>Bathrooms
                            </label>
                            <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', $unit->bathrooms) }}" min="1" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('bathrooms')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Max Guests -->
                        <div>
                            <label for="max_guests" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-users mr-2 text-indigo-500"></i>Max Guests
                            </label>
                            <input type="number" id="max_guests" name="max_guests" value="{{ old('max_guests', $unit->max_guests) }}" min="1" required 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('max_guests')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Size (sqm) -->
                        <div>
                            <label for="size_sqm" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-ruler-combined mr-2 text-indigo-500"></i>Size (sqm)
                            </label>
                            <input type="number" id="size_sqm" name="size_sqm" value="{{ old('size_sqm', $unit->size_sqm) }}" step="0.01" min="0" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('size_sqm')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="sm:col-span-2">
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>Address
                            </label>
                            <input type="text" id="address" name="address" value="{{ old('address', $unit->address) }}" 
                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                            @error('address')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-2 text-indigo-500"></i>Description
                            </label>
                            <textarea id="description" name="description" rows="4" 
                                      class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">{{ old('description', $unit->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Amenities -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-list-check mr-2 text-indigo-500"></i>Amenities
                            </label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-48 overflow-y-auto border border-gray-300 rounded-xl p-4">
                                @foreach($amenities as $amenity)
                                    <label class="flex items-center space-x-2">
                                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                               {{ in_array($amenity->id, old('amenities', $unit->amenities->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                            @error('amenities')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Pricing Section -->
                        <div class="sm:col-span-2">
                            <h3 class="text-lg font-medium text-gray-900 mb-4 border-b border-gray-200 pb-2">
                                <i class="fas fa-dollar-sign mr-2 text-indigo-500"></i>Pricing Information
                            </h3>
                            
                            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                <!-- Cleaning Fee -->
                                <div>
                                    <label for="cleaning_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-broom mr-2 text-indigo-500"></i>Cleaning Fee
                                    </label>
                                    <input type="number" id="cleaning_fee" name="cleaning_fee" value="{{ old('cleaning_fee') }}" step="0.01" min="0" 
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    @error('cleaning_fee')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Security Deposit -->
                                <div>
                                    <label for="security_deposit" class="block text-sm font-medium text-gray-700 mb-2">
                                        <i class="fas fa-shield-alt mr-2 text-indigo-500"></i>Security Deposit
                                    </label>
                                    <input type="number" id="security_deposit" name="security_deposit" value="{{ old('security_deposit') }}" step="0.01" min="0" 
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    @error('security_deposit')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <!-- Monthly Pricing (Daily Rates) -->
                            <div class="mt-8">
                                <h4 class="text-md font-semibold text-gray-900 mb-3">
                                    <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>Monthly Pricing (Daily Rate)
                                </h4>
                                <p class="text-sm text-gray-500 mb-4">Set the daily price per night for each month. These rates apply per night within the month.</p>
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                    @foreach(($months ?? []) as $m)
                                        @php
                                            $existingPrice = isset($existing[$m['year_month']]) ? $existing[$m['year_month']]->daily_price : null;
                                        @endphp
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                                <i class="fas fa-calendar mr-2 text-indigo-500"></i>{{ $m['label'] }} ({{ $m['year_month'] }})
                                            </label>
                                            <input type="number" name="monthly_price_{{ $m['key'] }}" value="{{ old('monthly_price_' . $m['key'], $existingPrice) }}" step="0.01" min="0" placeholder="Daily price" 
                                                   class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                            @error('monthly_price_' . $m['key'])
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-3">
                        <a href="{{ route('admin.units.show', $unit) }}" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-times mr-2"></i>
                            Cancel
                        </a>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                            <i class="fas fa-save mr-2"></i>
                            Update Unit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-admin-layout> 