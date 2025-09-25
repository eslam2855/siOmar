<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class LanguageController extends Controller
{
    /**
     * Switch the application language
     */
    public function switchLanguage(Request $request, $locale)
    {
        // Validate the locale
        $allowedLocales = ['en', 'ar'];
        
        if (!in_array($locale, $allowedLocales)) {
            $locale = 'en'; // Default to English
        }

        // Set the locale in session
        Session::put('locale', $locale);
        
        // Set the application locale
        App::setLocale($locale);

        // Redirect back to the previous page
        return redirect()->back();
    }

    /**
     * Get current language
     */
    public function getCurrentLanguage()
    {
        return response()->json([
            'locale' => App::getLocale(),
            'direction' => App::getLocale() === 'ar' ? 'rtl' : 'ltr'
        ]);
    }
}
