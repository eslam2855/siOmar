@php
    $currentLocale = app()->getLocale();
    $isRTL = $currentLocale === 'ar';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - Admin</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
    

    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback for when Vite is not running -->
    @if(!app()->environment('local'))
        <link rel="stylesheet" href="{{ asset('build/assets/app-CkXx0MGJ.css') }}">
        <script type="module" src="{{ asset('build/assets/app-C0G0cght.js') }}"></script>
    @endif
</head>
<body class="font-sans antialiased bg-gray-50 {{ $isRTL ? 'rtl' : 'ltr' }}">
    <div class="min-h-screen flex {{ $isRTL ? 'flex-row-reverse' : '' }}">
        <!-- Sidebar -->
        <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 {{ $isRTL ? 'md:right-0' : 'md:left-0' }}">
            <div class="flex-1 flex flex-col min-h-0 bg-gradient-to-b from-indigo-800 to-indigo-900 shadow-xl">
                <!-- Logo -->
                <div class="flex items-center h-16 flex-shrink-0 px-4 bg-indigo-900">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-building text-white text-2xl mr-3"></i>
                        </div>
                        <div>
                            <h1 class="text-white text-lg font-bold">{{ config('app.name', 'Laravel') }}</h1>
                            <p class="text-indigo-200 text-xs">{{ __('admin.dashboard') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="flex-1 flex flex-col overflow-y-auto">
                    <nav class="flex-1 px-2 py-4 space-y-1">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-tachometer-alt {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.dashboard') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.dashboard') }}
                        </a>
                        
                        <a href="{{ route('admin.reservations') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.reservations*') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-calendar-check {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.reservations*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.reservations') }}
                        </a>
                        
                        <a href="{{ route('admin.units') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.units*') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-home {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.units*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.units') }}
                        </a>
                        
                        <a href="{{ route('admin.sliders') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.sliders*') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-images {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.sliders*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.sliders') }}
                        </a>
                        
                        <a href="{{ route('admin.users') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.users*') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-users {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.users*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.users') }}
                        </a>
                        
                        <a href="{{ route('admin.settings.index') }}" 
                           class="group flex items-center px-2 py-2 text-sm font-medium rounded-md transition-all duration-200 {{ request()->routeIs('admin.settings*') ? 'bg-indigo-700 text-white shadow-lg' : 'text-indigo-100 hover:bg-indigo-700 hover:text-white' }}">
                            <i class="fas fa-cog {{ $isRTL ? 'ml-3' : 'mr-3' }} text-lg {{ request()->routeIs('admin.settings*') ? 'text-white' : 'text-indigo-300 group-hover:text-white' }}"></i>
                            {{ __('admin.settings') }}
                        </a>
                    </nav>
                </div>
                
                <!-- User Profile -->
                <div class="flex-shrink-0 flex border-t border-indigo-700 p-4">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="h-8 w-8 rounded-full bg-indigo-300 flex items-center justify-center">
                                <i class="fas fa-user text-indigo-700 text-sm"></i>
                            </div>
                        </div>
                        <div class="{{ $isRTL ? 'mr-3' : 'ml-3' }}">
                            <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-indigo-200">{{ Auth::user()->email }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <div class="{{ $isRTL ? 'md:pr-64' : 'md:pl-64' }} flex flex-col flex-1">
            <!-- Top navigation -->
            <div class="sticky top-0 z-10 md:hidden pl-1 pt-1 sm:pl-3 sm:pt-3 bg-white shadow-sm">
                <button type="button" class="-ml-0.5 -mt-0.5 h-12 w-12 inline-flex items-center justify-center rounded-md text-gray-500 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
            
            <!-- Header -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="flex justify-between items-center py-6">
                        <div class="flex items-center">
                            <h1 class="text-2xl font-bold text-gray-900">
                                @if(request()->routeIs('admin.dashboard'))
                                    <i class="fas fa-tachometer-alt {{ $isRTL ? 'ml-3' : 'mr-3' }} text-indigo-600"></i>{{ __('admin.dashboard') }}
                                @elseif(request()->routeIs('admin.reservations*'))
                                    <i class="fas fa-calendar-check {{ $isRTL ? 'ml-3' : 'mr-3' }} text-indigo-600"></i>{{ __('admin.reservations') }}
                                @elseif(request()->routeIs('admin.units*'))
                                    <i class="fas fa-home {{ $isRTL ? 'ml-3' : 'mr-3' }} text-indigo-600"></i>{{ __('admin.units') }}
                                @elseif(request()->routeIs('admin.sliders*'))
                                    <i class="fas fa-images {{ $isRTL ? 'ml-3' : 'mr-3' }} text-indigo-600"></i>{{ __('admin.sliders') }}
                                @elseif(request()->routeIs('admin.users*'))
                                    <i class="fas fa-users {{ $isRTL ? 'ml-3' : 'mr-3' }} text-indigo-600"></i>{{ __('admin.users') }}
                                @else
                                    {{ __('admin.dashboard') }}
                                @endif
                            </h1>
                        </div>
                        
                        <div class="flex items-center space-x-4">
                            <!-- Language Switcher -->
                            <x-language-switcher />
                            
                            <div class="relative">
                                <button type="button" class="bg-white p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <span class="sr-only">View notifications</span>
                                    <i class="fas fa-bell text-lg"></i>
                                </button>
                            </div>
                            
                            <div class="relative">
                                <form method="POST" action="{{ route('logout') }}" class="inline">
                                    @csrf
                                    <button type="submit" class="flex items-center text-sm text-gray-500 hover:text-gray-700 font-medium transition-colors">
                                        <i class="fas fa-sign-out-alt {{ $isRTL ? 'ml-2' : 'mr-2' }}"></i>
                                        {{ __('admin.logout') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1">
                <div class="py-8">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <!-- Alerts -->
                        @if(session('success'))
                            <div class="mb-6 bg-green-50 {{ $isRTL ? 'border-r-4' : 'border-l-4' }} border-green-400 p-4 {{ $isRTL ? 'rounded-l-lg' : 'rounded-r-lg' }} shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                    </div>
                                    <div class="{{ $isRTL ? 'mr-3' : 'ml-3' }}">
                                        <p class="text-sm font-medium text-green-800">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="mb-6 bg-red-50 {{ $isRTL ? 'border-r-4' : 'border-l-4' }} border-red-400 p-4 {{ $isRTL ? 'rounded-l-lg' : 'rounded-r-lg' }} shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
                                    </div>
                                    <div class="{{ $isRTL ? 'mr-3' : 'ml-3' }}">
                                        <p class="text-sm font-medium text-red-800">
                                            {{ session('error') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="mb-6 bg-red-50 {{ $isRTL ? 'border-r-4' : 'border-l-4' }} border-red-400 p-4 {{ $isRTL ? 'rounded-l-lg' : 'rounded-r-lg' }} shadow-sm">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                                    </div>
                                    <div class="{{ $isRTL ? 'mr-3' : 'ml-3' }}">
                                        <h3 class="text-sm font-medium text-red-800">
                                            {{ __('admin.validation_failed') }}:
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700">
                                            <ul class="list-disc list-inside space-y-1">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Page Content -->
                        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Mobile menu overlay -->
    <div class="hidden fixed inset-0 flex z-40 md:hidden" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75"></div>
        <div class="relative flex-1 flex flex-col max-w-xs w-full bg-indigo-800">
            <div class="absolute top-0 right-0 -mr-12 pt-2">
                <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white">
                    <span class="sr-only">Close sidebar</span>
                    <i class="fas fa-times text-white text-xl"></i>
                </button>
            </div>
        </div>
    </div>
</body>
</html> 