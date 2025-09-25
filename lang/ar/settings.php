<?php

return [
    // Settings Page
    'settings' => 'الإعدادات',
    'global_settings' => 'الإعدادات العامة',
    'reservation_settings' => 'إعدادات الحجز',
    'system_settings' => 'إعدادات النظام',
    'general_settings' => 'الإعدادات العامة',
    'payment_settings' => 'إعدادات الدفع',
    'notification_settings' => 'إعدادات الإشعارات',
    'security_settings' => 'إعدادات الأمان',

    // Reservation Settings
    'default_reservation_notes' => 'ملاحظات الحجز الافتراضية',
    'default_reservation_notes_help' => 'الملاحظات التي ستظهر تلقائياً في جميع الحجوزات الجديدة',
    'default_deposit_percentage' => 'نسبة الوديعة الافتراضية',
    'default_deposit_percentage_help' => 'النسبة المئوية من إجمالي المبلغ المطلوبة كوديعة (0-100)',
    'default_minimum_deposit_amount' => 'الحد الأدنى لمبلغ الوديعة الافتراضي',
    'default_minimum_deposit_amount_help' => 'المبلغ الثابت المطلوب كوديعة (يأخذ الأولوية على النسبة)',
    'reservation_auto_approve' => 'الموافقة التلقائية على الحجوزات',
    'reservation_auto_approve_help' => 'الموافقة التلقائية على الحجوزات بعد التحقق من الوديعة',
    'require_deposit_for_approval' => 'تطلب وديعية للموافقة',
    'require_deposit_for_approval_help' => 'تطلب تحميل إيصال وديعية قبل الموافقة على الحجز',

    // Legal Documents Settings
    'privacy_policy' => 'سياسة الخصوصية',
    'terms_of_service' => 'شروط الخدمة',
    'privacy_policy_help' => 'سيتم عرض هذا المحتوى للمستخدمين ويجب أن يتوافق مع قوانين الخصوصية المعمول بها',
    'terms_of_service_help' => 'سيتم عرض هذا المحتوى للمستخدمين ويجب أن يوضح الشروط والأحكام لاستخدام خدمتك',
    'privacy_policy_placeholder' => 'أدخل محتوى سياسة الخصوصية هنا...',
    'terms_of_service_placeholder' => 'أدخل محتوى شروط الخدمة هنا...',
    'required_min_10_chars' => 'مطلوب (الحد الأدنى 10 أحرف)',
    'legal_documents_important' => 'معلومات قانونية مهمة',
    'legal_documents_requirement_1' => 'تأكد من أن سياسة الخصوصية تتوافق مع قوانين حماية البيانات المعمول بها (GDPR، CCPA، إلخ)',
    'legal_documents_requirement_2' => 'يجب أن توضح شروط الخدمة بوضوح حقوق المستخدمين والمسؤوليات والقيود',
    'legal_documents_requirement_3' => 'فكر في استشارة محامٍ للامتثال',

    // Deposit Logic
    'deposit_priority_logic' => 'منطق أولوية الوديعة',
    'deposit_logic_explanation' => 'كيف يتم حساب مبلغ الوديعة المطلوب:',
    'fixed_amount_priority' => 'إذا تم تحديد مبلغ ثابت، فإنه يأخذ الأولوية',
    'percentage_calculation' => 'إذا تم تحديد النسبة فقط، يتم الحساب: (المبلغ الإجمالي × النسبة) ÷ 100',
    'no_requirement' => 'إذا لم يتم تحديد أي منهما، فلا توجد وديعية مطلوبة',
    'deposit_calculation_example' => 'مثال: إذا كان إجمالي المبلغ 1000 ج.م والنسبة 50%، فإن الوديعة المطلوبة = 500 ج.م',

    // Workflow Settings
    'reservation_workflow' => 'سير عمل الحجز',
    'approval_workflow' => 'سير عمل الموافقة',
    'deposit_workflow' => 'سير عمل الوديعة',
    'auto_approval_settings' => 'إعدادات الموافقة التلقائية',
    'deposit_verification_settings' => 'إعدادات التحقق من الوديعة',
    'enable_auto_approval' => 'تفعيل الموافقة التلقائية',
    'disable_auto_approval' => 'إلغاء الموافقة التلقائية',
    'require_deposit_verification' => 'تطلب التحقق من الوديعة',
    'skip_deposit_verification' => 'تخطي التحقق من الوديعة',

    // Payment Settings
    'payment_settings' => 'إعدادات الدفع',
    'payment_methods' => 'طرق الدفع',
    'bank_transfer' => 'التحويل البنكي',
    'cash_payment' => 'الدفع النقدي',
    'online_payment' => 'الدفع الإلكتروني',
    'payment_verification' => 'التحقق من الدفع',
    'automatic_verification' => 'التحقق التلقائي',
    'manual_verification' => 'التحقق اليدوي',
    'payment_timeout' => 'مهلة الدفع',
    'payment_timeout_help' => 'الوقت المسموح لإكمال الدفع (بالساعات)',

    // Notification Settings
    'notification_settings' => 'إعدادات الإشعارات',
    'email_notifications' => 'إشعارات البريد الإلكتروني',
    'sms_notifications' => 'إشعارات الرسائل النصية',
    'push_notifications' => 'إشعارات الدفع',
    'new_reservation_notification' => 'إشعار الحجز الجديد',
    'reservation_confirmed_notification' => 'إشعار تأكيد الحجز',
    'payment_received_notification' => 'إشعار استلام الدفع',
    'reservation_reminder_notification' => 'إشعار تذكير الحجز',

    // System Settings
    'system_settings' => 'إعدادات النظام',
    'timezone' => 'المنطقة الزمنية',
    'date_format' => 'تنسيق التاريخ',
    'time_format' => 'تنسيق الوقت',
    'currency' => 'العملة',
    'language' => 'اللغة',
    'maintenance_mode' => 'وضع الصيانة',
    'debug_mode' => 'وضع التصحيح',
    'log_level' => 'مستوى التسجيل',

    // Security Settings
    'security_settings' => 'إعدادات الأمان',
    'password_policy' => 'سياسة كلمة المرور',
    'minimum_password_length' => 'الحد الأدنى لطول كلمة المرور',
    'require_special_characters' => 'تطلب أحرف خاصة',
    'require_numbers' => 'تطلب أرقام',
    'require_uppercase' => 'تطلب أحرف كبيرة',
    'session_timeout' => 'مهلة الجلسة',
    'max_login_attempts' => 'الحد الأقصى لمحاولات تسجيل الدخول',
    'two_factor_authentication' => 'المصادقة الثنائية',

    // Form Labels
    'save_settings' => 'حفظ الإعدادات',
    'reset_settings' => 'إعادة تعيين الإعدادات',
    'apply_defaults' => 'تطبيق الإعدادات الافتراضية',
    'export_settings' => 'تصدير الإعدادات',
    'import_settings' => 'استيراد الإعدادات',
    'settings_saved' => 'تم حفظ الإعدادات بنجاح',
    'settings_reset' => 'تم إعادة تعيين الإعدادات بنجاح',
    'settings_exported' => 'تم تصدير الإعدادات بنجاح',
    'settings_imported' => 'تم استيراد الإعدادات بنجاح',

    // Validation Messages
    'validation' => [
        'percentage_range' => 'النسبة يجب أن تكون بين 0 و 100',
        'amount_positive' => 'المبلغ يجب أن يكون موجباً',
        'notes_max_length' => 'الملاحظات يجب ألا تتجاوز 1000 حرف',
        'timeout_positive' => 'المهلة يجب أن تكون موجباً',
        'password_length_min' => 'طول كلمة المرور يجب أن يكون على الأقل :min أحرف',
        'session_timeout_min' => 'مهلة الجلسة يجب أن تكون على الأقل :min دقيقة',
        'max_attempts_positive' => 'الحد الأقصى للمحاولات يجب أن يكون موجباً',
    ],

    // Help Text
    'help' => [
        'reservation_notes' => 'هذه الملاحظات ستظهر للضيوف في جميع الحجوزات الجديدة',
        'deposit_percentage' => 'النسبة المئوية من إجمالي مبلغ الحجز المطلوبة كوديعة',
        'minimum_deposit' => 'المبلغ الثابت المطلوب كوديعة (يأخذ الأولوية على النسبة)',
        'auto_approve' => 'عند التفعيل، ستتم الموافقة التلقائية على الحجوزات بعد التحقق من الوديعة',
        'require_deposit' => 'عند التفعيل، سيُطلب من الضيوف رفع إيصال وديعية قبل الموافقة',
        'payment_timeout' => 'الوقت المسموح للضيوف لإكمال الدفع قبل إلغاء الحجز تلقائياً',
        'notification_email' => 'عند التفعيل، سيتم إرسال إشعارات عبر البريد الإلكتروني',
        'notification_sms' => 'عند التفعيل، سيتم إرسال إشعارات عبر الرسائل النصية',
        'maintenance_mode' => 'عند التفعيل، سيكون النظام في وضع الصيانة ولن يكون متاحاً للمستخدمين',
        'debug_mode' => 'عند التفعيل، سيتم عرض رسائل التصحيح (للمطورين فقط)',
        'log_level' => 'مستوى تفصيل سجلات النظام (error, warning, info, debug)',
    ],

    // Default Values
    'defaults' => [
        'reservation_notes' => 'يرجى إرسال 50% من إجمالي مبلغ الحجز لتأكيد حجزك...',
        'deposit_percentage' => 50,
        'minimum_deposit_amount' => 0,
        'auto_approve' => false,
        'require_deposit' => true,
        'payment_timeout' => 24,
        'timezone' => 'Asia/Riyadh',
        'date_format' => 'Y-m-d',
        'time_format' => 'H:i',
        'currency' => 'EGP',
        'language' => 'ar',
        'maintenance_mode' => false,
        'debug_mode' => false,
        'log_level' => 'info',
        'minimum_password_length' => 8,
        'require_special_characters' => true,
        'require_numbers' => true,
        'require_uppercase' => true,
        'session_timeout' => 120,
        'max_login_attempts' => 5,
        'two_factor_authentication' => false,
    ],

    // Categories
    'categories' => [
        'reservation' => 'الحجز',
        'payment' => 'الدفع',
        'notification' => 'الإشعارات',
        'system' => 'النظام',
        'security' => 'الأمان',
        'appearance' => 'المظهر',
        'integration' => 'التكامل',
    ],

    // Status
    'enabled' => 'مفعل',
    'disabled' => 'معطل',
    'active' => 'نشط',
    'inactive' => 'غير نشط',
    'on' => 'تشغيل',
    'off' => 'إيقاف',
    'yes' => 'نعم',
    'no' => 'لا',
    'true' => 'صحيح',
    'false' => 'خطأ',

    // Actions
    'actions' => [
        'save' => 'حفظ',
        'cancel' => 'إلغاء',
        'reset' => 'إعادة تعيين',
        'apply' => 'تطبيق',
        'export' => 'تصدير',
        'import' => 'استيراد',
        'backup' => 'نسخ احتياطي',
        'restore' => 'استعادة',
        'clear_cache' => 'مسح الذاكرة المؤقتة',
        'optimize' => 'تحسين',
    ],

    // Messages
    'messages' => [
        'settings_saved_successfully' => 'تم حفظ الإعدادات بنجاح',
        'settings_reset_successfully' => 'تم إعادة تعيين الإعدادات بنجاح',
        'settings_exported_successfully' => 'تم تصدير الإعدادات بنجاح',
        'settings_imported_successfully' => 'تم استيراد الإعدادات بنجاح',
        'cache_cleared_successfully' => 'تم مسح الذاكرة المؤقتة بنجاح',
        'system_optimized_successfully' => 'تم تحسين النظام بنجاح',
        'backup_created_successfully' => 'تم إنشاء النسخة الاحتياطية بنجاح',
        'backup_restored_successfully' => 'تم استعادة النسخة الاحتياطية بنجاح',
        'confirm_reset_settings' => 'هل أنت متأكد من إعادة تعيين جميع الإعدادات؟',
        'confirm_clear_cache' => 'هل أنت متأكد من مسح الذاكرة المؤقتة؟',
        'confirm_optimize_system' => 'هل أنت متأكد من تحسين النظام؟',
        'confirm_backup_restore' => 'هل أنت متأكد من استعادة النسخة الاحتياطية؟ سيتم استبدال الإعدادات الحالية.',
    ],
];
