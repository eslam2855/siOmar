<x-admin-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Header Section -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-cog text-white text-xl"></i>
                            </div>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">{{ __('admin.settings_management') }}</h1>
                            <p class="text-gray-600 mt-1">{{ __('admin.manage_system_configuration') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="bg-white rounded-lg px-4 py-2 shadow-sm border border-gray-200">
                            <span class="text-sm text-gray-500">{{ __('admin.last_updated') }}:</span>
                            <span class="text-sm font-medium text-gray-900 ml-1">{{ now()->format('M d, Y H:i') }}</span>
                        </div>
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

            <!-- Settings Form -->
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
                @csrf
                
                <!-- Reservation Settings Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-calendar-check text-white text-xl mr-3"></i>
                            <h2 class="text-xl font-bold text-white">{{ __('admin.reservation_settings') }}</h2>
                        </div>
                        <p class="text-indigo-100 mt-1 text-sm">{{ __('admin.configure_reservation_workflow') }}</p>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        
                        <!-- Default Reservation Notes -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-sticky-note text-indigo-500 text-lg"></i>
                                <label for="default_reservation_notes" class="text-lg font-semibold text-gray-900">
                                    {{ __('admin.default_reservation_notes') }}
                                </label>
                            </div>
                            <div class="relative">
                                <textarea 
                                    id="default_reservation_notes" 
                                    name="default_reservation_notes" 
                                    rows="4" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="{{ __('admin.default_reservation_notes_help') }}"
                                >{{ $settings['default_reservation_notes'] ?? '' }}</textarea>
                                <div class="absolute bottom-3 right-3">
                                    <span class="text-xs text-gray-400">{{ __('admin.optional') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                {{ __('admin.default_reservation_notes_help') }}
                            </p>
                        </div>

                        <!-- Deposit Settings Grid -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            
                            <!-- Deposit Percentage -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-percentage text-green-500 text-lg"></i>
                                    <label for="default_deposit_percentage" class="text-lg font-semibold text-gray-900">
                                        {{ __('admin.default_deposit_percentage') }}
                                    </label>
                                </div>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0" 
                                        max="100" 
                                        id="default_deposit_percentage" 
                                        name="default_deposit_percentage" 
                                        value="{{ $settings['default_deposit_percentage'] ?? 0 }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200"
                                        placeholder="50"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 text-sm">%</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-info-circle text-green-500 mr-2"></i>
                                    {{ __('admin.default_deposit_percentage_help') }}
                                </p>
                            </div>

                            <!-- Minimum Deposit Amount -->
                            <div class="space-y-3">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-coins text-yellow-500 text-lg"></i>
                                    <label for="default_minimum_deposit_amount" class="text-lg font-semibold text-gray-900">
                                        {{ __('admin.default_minimum_deposit_amount') }}
                                    </label>
                                </div>
                                <div class="relative">
                                    <input 
                                        type="number" 
                                        step="0.01" 
                                        min="0" 
                                        id="default_minimum_deposit_amount" 
                                        name="default_minimum_deposit_amount" 
                                        value="{{ $settings['default_minimum_deposit_amount'] ?? 0 }}"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all duration-200"
                                        placeholder="0.00"
                                    >
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <span class="text-gray-500 text-sm">{{ __('admin.currency_symbol') }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 flex items-center">
                                    <i class="fas fa-info-circle text-yellow-500 mr-2"></i>
                                    {{ __('admin.default_minimum_deposit_amount_help') }}
                                </p>
                            </div>
                        </div>

                        <!-- Minimum Reservation Days -->
                        <div class="space-y-3">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-calendar-day text-red-500 text-lg"></i>
                                <label for="minimum_reservation_days" class="text-lg font-semibold text-gray-900">
                                    {{ __('admin.minimum_reservation_days') }}
                                </label>
                            </div>
                            <div class="relative">
                                <input 
                                    type="number" 
                                    min="1" 
                                    max="365" 
                                    id="minimum_reservation_days" 
                                    name="minimum_reservation_days" 
                                    value="{{ $settings['minimum_reservation_days'] ?? 1 }}"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200"
                                    placeholder="1"
                                >
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3">
                                    <span class="text-gray-500 text-sm">{{ __('admin.days') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-red-500 mr-2"></i>
                                {{ __('admin.minimum_reservation_days_help') }}
                            </p>
                        </div>

                        <!-- Approval Workflow -->
                        <div class="space-y-4">
                            <div class="flex items-center space-x-2">
                                <i class="fas fa-workflow text-purple-500 text-lg"></i>
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('admin.approval_workflow') }}</h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                
                                <!-- Require Deposit for Approval -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <input 
                                                id="require_deposit_for_approval" 
                                                name="require_deposit_for_approval" 
                                                type="checkbox" 
                                                value="1" 
                                                {{ ($settings['require_deposit_for_approval'] ?? true) ? 'checked' : '' }}
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-all duration-200"
                                            >
                                        </div>
                                        <div class="flex-1">
                                            <label for="require_deposit_for_approval" class="text-sm font-medium text-gray-900 cursor-pointer">
                                                {{ __('admin.require_deposit_for_approval') }}
                                            </label>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ __('admin.require_deposit_for_approval_help') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Auto Approve Reservations -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <input 
                                                id="reservation_auto_approve" 
                                                name="reservation_auto_approve" 
                                                type="checkbox" 
                                                value="1" 
                                                {{ ($settings['reservation_auto_approve'] ?? false) ? 'checked' : '' }}
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition-all duration-200"
                                            >
                                        </div>
                                        <div class="flex-1">
                                            <label for="reservation_auto_approve" class="text-sm font-medium text-gray-900 cursor-pointer">
                                                {{ __('admin.reservation_auto_approve') }}
                                            </label>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ __('admin.reservation_auto_approve_help') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Priority Logic Info Card -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-lightbulb text-blue-600 text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-blue-900 mb-3">{{ __('admin.deposit_priority_logic') }}</h3>
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="text-sm text-blue-800">{{ __('admin.fixed_amount_priority') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="text-sm text-blue-800">{{ __('admin.percentage_calculation') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            <span class="text-sm text-blue-800">{{ __('admin.no_requirement') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Legal Documents Card -->
                <div class="bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-teal-600 px-6 py-4">
                        <div class="flex items-center">
                            <i class="fas fa-file-contract text-white text-xl mr-3"></i>
                            <h2 class="text-xl font-bold text-white">{{ __('admin.legal_documents') }}</h2>
                        </div>
                        <p class="text-green-100 mt-1 text-sm">{{ __('admin.manage_privacy_policy_terms') }}</p>
                    </div>
                    
                    <div class="p-6 space-y-8">
                        
                        <!-- Privacy Policy -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-shield-alt text-green-500 text-lg"></i>
                                    <label for="privacy_policy" class="text-lg font-semibold text-gray-900">
                                        {{ __('admin.privacy_policy') }}
                                    </label>
                                </div>
                                @if($settings['privacy_policy_last_updated'])
                                <div class="text-sm text-gray-500">
                                    {{ __('admin.last_updated') }}: {{ \Carbon\Carbon::parse($settings['privacy_policy_last_updated'])->format('M d, Y') }}
                                </div>
                                @endif
                            </div>
                            <div class="relative">
                                <textarea 
                                    id="privacy_policy" 
                                    name="privacy_policy" 
                                    rows="8" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="{{ __('admin.privacy_policy_placeholder') }}"
                                >{{ $settings['privacy_policy'] ?? '' }}</textarea>
                                <div class="absolute bottom-3 right-3">
                                    <span class="text-xs text-gray-400">{{ __('admin.required_min_10_chars') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-green-500 mr-2"></i>
                                {{ __('admin.privacy_policy_help') }}
                            </p>
                        </div>

                        <!-- Terms of Service -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-gavel text-blue-500 text-lg"></i>
                                    <label for="terms_of_service" class="text-lg font-semibold text-gray-900">
                                        {{ __('admin.terms_of_service') }}
                                    </label>
                                </div>
                                @if($settings['terms_of_service_last_updated'])
                                <div class="text-sm text-gray-500">
                                    {{ __('admin.last_updated') }}: {{ \Carbon\Carbon::parse($settings['terms_of_service_last_updated'])->format('M d, Y') }}
                                </div>
                                @endif
                            </div>
                            <div class="relative">
                                <textarea 
                                    id="terms_of_service" 
                                    name="terms_of_service" 
                                    rows="8" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="{{ __('admin.terms_of_service_placeholder') }}"
                                >{{ $settings['terms_of_service'] ?? '' }}</textarea>
                                <div class="absolute bottom-3 right-3">
                                    <span class="text-xs text-gray-400">{{ __('admin.required_min_10_chars') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                {{ __('admin.terms_of_service_help') }}
                            </p>
                        </div>

                        <!-- Cancellation Policy -->
                        <div class="space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-times-circle text-red-500 text-lg"></i>
                                    <label for="cancellation_policy" class="text-lg font-semibold text-gray-900">
                                        {{ __('admin.cancellation_policy') }}
                                    </label>
                                </div>
                                @if($settings['cancellation_policy_last_updated'])
                                <div class="text-sm text-gray-500">
                                    {{ __('admin.last_updated') }}: {{ \Carbon\Carbon::parse($settings['cancellation_policy_last_updated'])->format('M d, Y') }}
                                </div>
                                @endif
                            </div>
                            <div class="relative">
                                <textarea 
                                    id="cancellation_policy" 
                                    name="cancellation_policy" 
                                    rows="8" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200 resize-none"
                                    placeholder="{{ __('admin.cancellation_policy_placeholder') }}"
                                >{{ $settings['cancellation_policy'] ?? '' }}</textarea>
                                <div class="absolute bottom-3 right-3">
                                    <span class="text-xs text-gray-400">{{ __('admin.required_min_10_chars') }}</span>
                                </div>
                            </div>
                            <p class="text-sm text-gray-600 flex items-center">
                                <i class="fas fa-info-circle text-red-500 mr-2"></i>
                                {{ __('admin.cancellation_policy_help') }}
                            </p>
                        </div>

                        <!-- Legal Documents Info Card -->
                        <div class="bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-xl p-6">
                            <div class="flex items-start space-x-4">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-amber-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-amber-600 text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-semibold text-amber-900 mb-3">{{ __('admin.legal_documents_important') }}</h3>
                                    <div class="space-y-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                            <span class="text-sm text-amber-800">{{ __('admin.legal_documents_requirement_1') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                            <span class="text-sm text-amber-800">{{ __('admin.legal_documents_requirement_2') }}</span>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <div class="w-2 h-2 bg-amber-500 rounded-full"></div>
                                            <span class="text-sm text-amber-800">{{ __('admin.legal_documents_requirement_3') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <button type="button" onclick="resetForm()" class="flex items-center space-x-2 px-6 py-3 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-200">
                            <i class="fas fa-undo text-gray-500"></i>
                            <span>{{ __('admin.reset_form') }}</span>
                        </button>
                        <button type="button" onclick="previewSettings()" class="flex items-center space-x-2 px-6 py-3 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition-all duration-200">
                            <i class="fas fa-eye text-gray-300"></i>
                            <span>{{ __('admin.preview_settings') }}</span>
                        </button>
                    </div>
                    
                    <div class="flex items-center space-x-4">
                        <button type="button" onclick="saveAsDraft()" class="flex items-center space-x-2 px-6 py-3 border border-indigo-300 text-indigo-700 rounded-xl hover:bg-indigo-50 transition-all duration-200">
                            <i class="fas fa-save text-indigo-500"></i>
                            <span>{{ __('admin.save_draft') }}</span>
                        </button>
                        <button type="submit" class="flex items-center space-x-2 px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-xl hover:from-indigo-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg">
                            <i class="fas fa-check text-white"></i>
                            <span class="font-semibold">{{ __('admin.save_settings') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript for enhanced functionality -->
    <script>
        function resetForm() {
            if (confirm('{{ __("admin.confirm_reset_form") }}')) {
                document.querySelector('form').reset();
            }
        }

        function previewSettings() {
            // Show a preview modal or alert with current settings
            const formData = new FormData(document.querySelector('form'));
            let preview = '{{ __("admin.settings_preview") }}:\n\n';
            
            for (let [key, value] of formData.entries()) {
                if (value) {
                    preview += `${key}: ${value}\n`;
                }
            }
            
            alert(preview);
        }

        function saveAsDraft() {
            // Save current form state to localStorage
            const formData = new FormData(document.querySelector('form'));
            const draft = {};
            
            for (let [key, value] of formData.entries()) {
                draft[key] = value;
            }
            
            localStorage.setItem('settings_draft', JSON.stringify(draft));
            alert('{{ __("admin.draft_saved") }}');
        }

        // Auto-save draft every 30 seconds
        setInterval(() => {
            const formData = new FormData(document.querySelector('form'));
            const draft = {};
            
            for (let [key, value] of formData.entries()) {
                draft[key] = value;
            }
            
            localStorage.setItem('settings_draft', JSON.stringify(draft));
        }, 30000);

        // Load draft on page load
        document.addEventListener('DOMContentLoaded', function() {
            const draft = localStorage.getItem('settings_draft');
            if (draft) {
                const draftData = JSON.parse(draft);
                for (let key in draftData) {
                    const element = document.querySelector(`[name="${key}"]`);
                    if (element) {
                        element.value = draftData[key];
                    }
                }
            }
        });
    </script>
</x-admin-layout>
