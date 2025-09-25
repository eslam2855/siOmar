<x-admin-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-900">{{ __('admin.create') }} {{ __('admin.add_new') }} {{ __('admin.unit') }}</h1>
                <a href="{{ route('admin.units') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md text-sm font-medium">
                    {{ __('admin.back') }} {{ __('admin.to') }} {{ __('admin.units') }}
                </a>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 md:px-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form method="POST" action="{{ route('admin.units.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Unit Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-home mr-2 text-indigo-500"></i>{{ __('admin.unit_name') }}
                                </label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unit Number -->
                            <div>
                                <label for="unit_number" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-hashtag mr-2 text-indigo-500"></i>{{ __('admin.unit_number') }}
                                </label>
                                <input type="text" id="unit_number" name="unit_number" value="{{ old('unit_number') }}" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('unit_number')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Unit Type -->
                            <div>
                                <label for="unit_type_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-building mr-2 text-indigo-500"></i>{{ __('admin.unit_type') }}
                                </label>
                                <select id="unit_type_id" name="unit_type_id" required 
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <option value="">{{ __('admin.select_unit_type') }}</option>
                                    @foreach($unitTypes as $unitType)
                                        <option value="{{ $unitType->id }}" {{ old('unit_type_id') == $unitType->id ? 'selected' : '' }}>
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
                                    <i class="fas fa-info-circle mr-2 text-indigo-500"></i>{{ __('admin.status') }}
                                </label>
                                <select id="status" name="status" required 
                                        class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                    <option value="">{{ __('admin.select_status') }}</option>
                                    <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>{{ __('admin.available') }}</option>
                                    <option value="occupied" {{ old('status') == 'occupied' ? 'selected' : '' }}>{{ __('admin.occupied') }}</option>
                                    <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>{{ __('admin.maintenance') }}</option>
                                    <option value="reserved" {{ old('status') == 'reserved' ? 'selected' : '' }}>{{ __('admin.reserved') }}</option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bedrooms -->
                            <div>
                                <label for="bedrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-bed mr-2 text-indigo-500"></i>{{ __('admin.bedrooms') }}
                                </label>
                                <input type="number" id="bedrooms" name="bedrooms" value="{{ old('bedrooms', 1) }}" min="1" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('bedrooms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Bathrooms -->
                            <div>
                                <label for="bathrooms" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-bath mr-2 text-indigo-500"></i>{{ __('admin.bathrooms') }}
                                </label>
                                <input type="number" id="bathrooms" name="bathrooms" value="{{ old('bathrooms', 1) }}" min="1" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('bathrooms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Max Guests -->
                            <div>
                                <label for="max_guests" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-users mr-2 text-indigo-500"></i>{{ __('admin.max_guests') }}
                                </label>
                                <input type="number" id="max_guests" name="max_guests" value="{{ old('max_guests') }}" min="1" required 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('max_guests')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Size (sqm) -->
                            <div>
                                <label for="size_sqm" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-ruler-combined mr-2 text-indigo-500"></i>{{ __('admin.size_sqm') }}
                                </label>
                                <input type="number" id="size_sqm" name="size_sqm" value="{{ old('size_sqm') }}" step="0.01" min="0" 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('size_sqm')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Address -->
                            <div class="sm:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-map-marker-alt mr-2 text-indigo-500"></i>{{ __('admin.address') }}
                                </label>
                                <input type="text" id="address" name="address" value="{{ old('address') }}" 
                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-align-left mr-2 text-indigo-500"></i>{{ __('admin.description') }}
                                </label>
                                <textarea id="description" name="description" rows="4" 
                                          class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Amenities -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-list-check mr-2 text-indigo-500"></i>{{ __('admin.amenities') }}
                                </label>
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-48 overflow-y-auto border border-gray-300 rounded-md p-4">
                                    @foreach($amenities as $amenity)
                                        <label class="flex items-center space-x-2">
                                            <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" 
                                                   {{ in_array($amenity->id, old('amenities', [])) ? 'checked' : '' }}
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
                                    <i class="fas fa-dollar-sign mr-2 text-indigo-500"></i>{{ __('admin.pricing_information') }}
                                </h3>
                                
                                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <!-- Cleaning Fee -->
                                    <div>
                                        <label for="cleaning_fee" class="block text-sm font-medium text-gray-700 mb-2">
                                            <i class="fas fa-broom mr-2 text-indigo-500"></i>{{ __('admin.cleaning_fee') }}
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
                                            <i class="fas fa-shield-alt mr-2 text-indigo-500"></i>{{ __('admin.security_deposit') }}
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
                                    <p class="text-sm text-gray-500 mb-4">Set the daily price per night for each month below.</p>
                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                                        @foreach(($months ?? []) as $m)
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                    <i class="fas fa-calendar mr-2 text-indigo-500"></i>{{ $m['label'] }} ({{ $m['year_month'] }})
                                                </label>
                                                <input type="number" name="monthly_price_{{ $m['key'] }}" value="{{ old('monthly_price_' . $m['key']) }}" step="0.01" min="0" placeholder="Daily price" 
                                                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200">
                                                @error('monthly_price_' . $m['key'])
                                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Images -->
                            <div class="sm:col-span-2">
                                <label for="images" class="block text-sm font-medium text-gray-700">
                                    <i class="fas fa-images mr-2 text-indigo-500"></i>{{ __('admin.images') }}
                                </label>
                                <input type="file" id="images" name="images[]" multiple accept="image/*" 
                                       class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">{{ __('admin.max_5_images_2mb_each') }}</p>
                                @error('images.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <a href="{{ route('admin.units') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                                {{ __('admin.cancel') }}
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                                {{ __('admin.create_unit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout> 