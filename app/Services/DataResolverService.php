<?php

namespace App\Services;

class DataResolverService
{
    public static function resolve(string $type): array
    {
        $data = require resource_path('data/data.php');

        return $data[$type] ?? [];
    }
}
