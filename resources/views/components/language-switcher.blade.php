@php
    $currentLocale = app()->getLocale();
    $isRTL = $currentLocale === 'ar';
@endphp

<div class="relative inline-block text-left">
    <div class="flex items-center space-x-2">
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            <i class="fas fa-globe mr-1"></i>
            {{ __('admin.language') }}
        </span>
        
        <div class="flex items-center space-x-1">
            <!-- English Flag -->
            <a href="{{ route('language.switch', 'en') }}" 
               class="flex items-center px-2 py-1 text-sm rounded-md transition-colors duration-200 {{ $currentLocale === 'en' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                <span class="text-xs mr-1">🇺🇸</span>
                <span class="hidden sm:inline">{{ __('admin.english') }}</span>
            </a>
            
            <!-- Arabic Flag -->
            <a href="{{ route('language.switch', 'ar') }}" 
               class="flex items-center px-2 py-1 text-sm rounded-md transition-colors duration-200 {{ $currentLocale === 'ar' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300' : 'text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700' }}">
                <span class="text-xs mr-1">🇸🇦</span>
                <span class="hidden sm:inline">{{ __('admin.arabic') }}</span>
            </a>
        </div>
    </div>
</div>
