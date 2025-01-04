<?php

namespace App\Services;

use App\Models\Category;
use App\Models\LegalContent;
use App\Models\Configuration;
use App\Models\WebsiteSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Mail\Mailables\Content;

class CacheService
{
    public static $duration;

    function __construct () {
        self::$duration = now()->addMinutes(10);
    }

    private static function cachePrefix()
    {
        $iso = strtoupper('NG');
		if (env('APP_ENV') != 'local') {
			$iso = isset($_SERVER["HTTP_CF_IPCOUNTRY"]) ? strtoupper($_SERVER["HTTP_CF_IPCOUNTRY"]) : 'NG';
		};

        return $iso;
    }


    static function remove($key)
    {
        return Cache::forget($key);
    }

    static function removeAll()
    {
        return Cache::flush();
    }

}
