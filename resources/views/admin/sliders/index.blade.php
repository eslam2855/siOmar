<x-admin-layout>
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-900">{{ __('admin.sliders_management') }}</h2>
                <a href="{{ route('admin.sliders.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    {{ __('admin.add_new_slider') }}
                </a>
            </div>

            <!-- Search and Filter -->
            <div class="mb-6">
                <form method="GET" action="{{ route('admin.sliders') }}" class="flex gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="{{ __('admin.search_by_title') }}" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <select name="status" class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">{{ __('admin.all_statuses') }}</option>
                            <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('admin.active') }}</option>
                            <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('admin.inactive') }}</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        {{ __('admin.filter') }}
                    </button>
                    <a href="{{ route('admin.sliders') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                        {{ __('admin.clear') }}
                    </a>
                </form>
            </div>

            <!-- Sliders Table -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.display_order') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.slider_image') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.slider_title') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.status') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.created_at') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('admin.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sliders as $slider)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $slider->order }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($slider->image)
                                        <img src="{{ asset('storage/' . $slider->image) }}" 
                                             alt="{{ $slider->title }}" 
                                             class="h-16 w-24 object-cover rounded">
                                    @else
                                        <div class="h-16 w-24 bg-gray-200 rounded flex items-center justify-center">
                                            <span class="text-gray-500 text-xs">{{ __('admin.no_image_available') }}</span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $slider->title }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $slider->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $slider->is_active ? __('admin.active') : __('admin.inactive') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $slider->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <a href="{{ route('admin.sliders.edit', $slider) }}" 
                                           class="text-indigo-600 hover:text-indigo-900">{{ __('admin.edit') }}</a>
                                        
                                        <form method="POST" action="{{ route('admin.sliders.toggle', $slider) }}" class="inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-{{ $slider->is_active ? 'yellow' : 'green' }}-600 hover:text-{{ $slider->is_active ? 'yellow' : 'green' }}-900">
                                                {{ $slider->is_active ? __('admin.deactivate') : __('admin.activate') }}
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.sliders.destroy', $slider) }}" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900"
                                                    onclick="return confirm('{{ __('admin.confirm_delete_slider') }}')">
                                                {{ __('admin.delete') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    {{ __('admin.no_sliders_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($sliders->hasPages())
                <div class="mt-6">
                    {{ $sliders->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
