<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class DataResolverService
{
    public static function resolve(string $type): array
    {
        $data = Cache::rememberForever('data', function () {
            return require resource_path('data/data.php');
        });

        return $data[$type] ?? [];
    }
}
