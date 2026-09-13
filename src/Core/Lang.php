<?php

namespace Core;

class Lang {
    private static $locale = 'en';
    private static $translations = [];
    private static $initialized = false;

    /**
     * Initialize language system — call once at the start.
     */
    public static function init() {
        if (self::$initialized) return;

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check URL param first, then session, default to 'en'
        if (isset($_GET['lang']) && in_array($_GET['lang'], ['en', 'bn'])) {
            self::$locale = $_GET['lang'];
            $_SESSION['lang'] = self::$locale;
        } elseif (isset($_SESSION['lang'])) {
            self::$locale = $_SESSION['lang'];
        }

        self::loadTranslations();
        self::$initialized = true;
    }

    /**
     * Load translation file for current locale.
     */
    private static function loadTranslations() {
        $file = __DIR__ . '/../../config/lang/' . self::$locale . '.php';
        if (file_exists($file)) {
            self::$translations = require $file;
        }
    }

    /**
     * Get translation by key, with optional replacements.
     * Usage: Lang::get('products_only_left', ['count' => 5])
     */
    public static function get($key, $replacements = []) {
        $text = self::$translations[$key] ?? $key;

        foreach ($replacements as $search => $replace) {
            $text = str_replace(':' . $search, $replace, $text);
        }

        return $text;
    }

    /**
     * Get current locale.
     */
    public static function locale() {
        return self::$locale;
    }

    /**
     * Get the other locale for switching.
     */
    public static function otherLocale() {
        return self::$locale === 'en' ? 'bn' : 'en';
    }

    /**
     * Set locale explicitly.
     */
    public static function setLocale($locale) {
        if (in_array($locale, ['en', 'bn'])) {
            self::$locale = $locale;
            $_SESSION['lang'] = $locale;
            self::$translations = [];
            self::loadTranslations();
        }
    }
}

/**
 * Global shortcut helper function.
 * Usage: __('key') or __('key', ['count' => 5])
 */
function __($key, $replacements = []) {
    return Lang::get($key, $replacements);
}
