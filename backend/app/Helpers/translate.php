<?php

if (!function_exists('translate')) {
    function translate(?array $translatableField, ?string $lang = null): string
    {
        $lang = $lang ?? app()->getLocale();

        // Fallback to Spanish if the translation doesn't exist
        return $translatableField[$lang] ?? $translatableField['es'] ?? '';
    }
}