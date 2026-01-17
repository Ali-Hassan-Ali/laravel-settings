<?php

use \App\Services\Settings\SettingsServices;


if (!function_exists('setting')) {
 
    function setting(?string $key, ?string $lang = null): SettingsServices
    {
        return new SettingsServices($key, $lang);

    }
}