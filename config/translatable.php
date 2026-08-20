<?php

use Astrotomic\Translatable\Validation\RuleFactory;

return [
    'locales' => ['en', 'ar'],
    'locale_separator' => '-',
    'locale' => null,
    'use_fallback' => true,
    'use_property_fallback' => true,
    'fallback_locale' => 'en',
    'translation_model_namespace' => null,
    'translation_suffix' => 'Translation',
    'locale_key' => 'locale',
    'to_array_always_loads_translations' => false,
    'rule_factory' => [
        'format' => RuleFactory::FORMAT_ARRAY,
        'prefix' => '%',
        'suffix' => '%',
    ],
];
