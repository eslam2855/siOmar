<x-admin-layout>
    <div class="p-6">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center space-x-3">
                        <a href="{{ route('admin.units') }}" class="text-indigo-600 hover:text-indigo-700">
                            <i class="fas fa-arrow-left text-xl"></i>
                        </a>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ $unit->name }}</h1>
                            <p class="text-sm text-gray-600">Unit #{{ $unit->unit_number }}</p>
                        </div>
                    </div>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <a href="{{ route('admin.units.edit', $unit) }}" 
                       class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 border border-transparent rounded-xl shadow-sm text-sm font-medium text-white hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Unit
                    </a>
                </div>
            </div>
        </div>

        <!-- Unit Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Basic Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-building text-blue-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Unit Type</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $unit->unitType->name }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-info-circle text-green-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                        @if($unit->status === 'available') bg-green-100 text-green-800
                                        @elseif($unit->status === 'occupied') bg-red-100 text-red-800
                                        @elseif($unit->status === 'maintenance') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        <i class="fas fa-circle mr-1.5 text-xs"></i>
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-bed text-purple-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Bedrooms</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $unit->bedrooms }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-pink-100 flex items-center justify-center">
                                        <i class="fas fa-bath text-pink-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Bathrooms</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $unit->bathrooms }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-users text-indigo-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Max Guests</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $unit->max_guests }}</p>
                                </div>
                            </div>
                            
                            @if($unit->size_sqm)
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-lg bg-yellow-100 flex items-center justify-center">
                                        <i class="fas fa-ruler-combined text-yellow-600"></i>
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Size</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $unit->size_sqm }} sqm</p>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                @if($unit->description)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Description</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-gray-700 leading-relaxed">{{ $unit->description }}</p>
                    </div>
                </div>
                @endif

                <!-- Pricing Information -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Pricing Information</h3>
                    </div>
                    <div class="p-6">
                        <!-- Monthly Pricing Summary -->
                        <div>
                            <h4 class="text-md font-semibold text-gray-900 mb-3">
                                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i>Monthly Pricing (Daily Rates)
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @php
                                    $now = now();
                                    $months = [
                                        ['label' => $now->format('F Y'), 'ym' => $now->format('Y-m')],
                                        ['label' => $now->copy()->addMonth()->format('F Y'), 'ym' => $now->copy()->addMonth()->format('Y-m')],
                                        ['label' => $now->copy()->addMonths(2)->format('F Y'), 'ym' => $now->copy()->addMonths(2)->format('Y-m')],
                                    ];
                                    $byYm = $unit->monthPrices->keyBy('year_month');
                                @endphp
                                @foreach($months as $m)
                                    <div class="p-4 border rounded-lg bg-gray-50">
                                        <div class="text-sm text-gray-500">{{ $m['label'] }}</div>
                                        <div class="text-lg font-semibold text-gray-900">
                                            @if(isset($byYm[$m['ym']]))
                                                {{ number_format($byYm[$m['ym']]->daily_price, 2) }} {{ $byYm[$m['ym']]->currency }}/night
                                            @else
                                                <span class="text-gray-400">Not set</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Amenities -->
                @if($unit->amenities && $unit->amenities->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Amenities</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($unit->amenities as $amenity)
                            <div class="flex items-center space-x-2">
                                <div class="flex-shrink-0">
                                    <div class="h-6 w-6 rounded-lg bg-indigo-100 flex items-center justify-center">
                                        <i class="fas fa-check text-indigo-600 text-xs"></i>
                                    </div>
                                </div>
                                <span class="text-sm text-gray-700">{{ $amenity->name }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Images Management -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">Images</h3>
                            <button type="button" onclick="toggleImageUploadForm()" 
                                    class="inline-flex items-center px-3 py-1 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                                <i class="fas fa-plus mr-2"></i>
                                Add Images
                            </button>
                        </div>
                    </div>
                    
                    <!-- Image Upload Form -->
                    <div id="imageUploadForm" class="hidden px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <form method="POST" action="{{ route('admin.units.images.upload', $unit) }}" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    <i class="fas fa-images mr-2 text-indigo-500"></i>Select Images
                                </label>
                                <input type="file" name="images[]" multiple accept="image/*" required 
                                       onchange="previewImages(this)"
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Maximum 5 images, 2MB each. Supported formats: JPEG, PNG, JPG, GIF</p>
                            </div>
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="flex flex-wrap gap-2"></div>
                            
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="toggleImageUploadForm()" 
                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all duration-200">
                                    Cancel
                                </button>
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload Images
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="p-6">
                        @if($unit->images && $unit->images->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($unit->images->sortBy('order') as $image)
                                @php
                                    $imageUrl = asset('storage/' . $image->image_path);
                                    $imageExists = file_exists(storage_path('app/public/' . $image->image_path));
                                @endphp
                                <div class="relative group">
                                    <div class="relative h-48 rounded-lg overflow-hidden bg-gray-200">
                                        @if($imageExists)
                                            <img src="{{ $imageUrl }}" 
                                                 alt="{{ $image->caption ?? 'Unit image' }}"
                                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200"
                                                 style="min-height: 192px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                                 onload="console.log('Image loaded successfully:', '{{ $imageUrl }}')">
                                            <!-- Fallback if image fails to load -->
                                            <div class="hidden absolute inset-0 flex items-center justify-center bg-gray-200">
                                                <div class="text-center">
                                                    <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                                    <p class="text-sm text-gray-500">Image not available</p>
                                                </div>
                                            </div>
                                        @else
                                            <div class="absolute inset-0 flex items-center justify-center bg-gray-200">
                                                <div class="text-center">
                                                    <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                                    <p class="text-sm text-gray-500">Image not found</p>
                                                    <p class="text-xs text-gray-400">{{ $image->image_path }}</p>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Image Overlay -->
                                    <div class="absolute inset-0  bg-opacity-0 group-hover:bg-opacity-30 transition-all duration-200 rounded-lg">
                                        <div class="absolute top-2 right-2 flex space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                            @if(!$image->is_primary)
                                            <form method="POST" action="{{ route('admin.units.images.primary', [$unit, $image->id]) }}" class="inline">
                                                @csrf
                                                <button type="submit" class="p-1 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition-colors">
                                                    <i class="fas fa-star text-xs"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <form method="POST" action="{{ route('admin.units.images.delete', [$unit, $image->id]) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this image?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors">
                                                    <i class="fas fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                        
                                        <!-- Primary Badge -->
                                        @if($image->is_primary)
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-star mr-1"></i>
                                                Primary
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Caption -->
                                    @if($image->caption)
                                    <div class="mt-2">
                                        <p class="text-sm text-gray-600">{{ $image->caption }}</p>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                                    <i class="fas fa-images text-3xl text-gray-400"></i>
                                </div>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">No images yet</h3>
                                <p class="text-gray-500 mb-4">Add some images to showcase this unit.</p>
                                <button type="button" onclick="toggleImageUploadForm()" 
                                        class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-700 text-white text-sm font-medium rounded-lg hover:from-indigo-700 hover:to-indigo-800 transition-all duration-200">
                                    <i class="fas fa-plus mr-2"></i>
                                    Add Images
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Quick Stats</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                        <i class="fas fa-calendar-check text-blue-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Total Reservations</span>
                                </div>
                                <span class="text-lg font-semibold text-gray-900">{{ $unit->reservations->count() }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                                        <i class="fas fa-star text-green-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Average Rating</span>
                                </div>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ $unit->reviews->count() > 0 ? number_format($unit->reviews->avg('rating'), 1) : 'N/A' }}
                                </span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                        <i class="fas fa-images text-purple-600 text-sm"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-700">Images</span>
                                </div>
                                <span class="text-lg font-semibold text-gray-900">{{ $unit->images->count() }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Reservations -->
                @if($unit->reservations && $unit->reservations->count() > 0)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Recent Reservations</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-3">
                            @foreach($unit->reservations->take(3) as $reservation)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $reservation->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $reservation->created_at->format('M d, Y') }}</p>
                                </div>
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($reservation->status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($reservation->status === 'confirmed') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800
                                    @endif">
                                    {{ ucfirst($reservation->status) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Address Information -->
                @if($unit->address)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Location</h3>
                    </div>
                    <div class="p-6">
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0">
                                <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                    <i class="fas fa-map-marker-alt text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            <div>
                                <p class="text-sm text-gray-700">{{ $unit->address }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        // Image upload form toggle
        function toggleImageUploadForm() {
            const form = document.getElementById('imageUploadForm');
            form.classList.toggle('hidden');
        }

        // Image preview functionality
        function previewImages(input) {
            const previewContainer = document.getElementById('imagePreview');
            previewContainer.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                for (let i = 0; i < input.files.length; i++) {
                    const file = input.files[i];
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative inline-block mr-2 mb-2';
                        preview.innerHTML = `
                            <img src="${e.target.result}" class="w-20 h-20 object-cover rounded-lg border">
                            <button type="button" onclick="this.parentElement.remove()" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                <i class="fas fa-times"></i>
                            </button>
                        `;
                        previewContainer.appendChild(preview);
                    };
                    
                    reader.readAsDataURL(file);
                }
            }
        }
    </script>
</x-admin-layout> 