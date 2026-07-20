<?php

namespace Config;

use CodeIgniter\Config\Filters as BaseFilters;
use App\Filters\AuthFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'=> \CodeIgniter\Filters\CSRF::class,
        'auth'=> AuthFilter::class,
        'role' => RoleFilter::class,
        'forcehttps' => ForceHTTPS::class,
        'pagecache' => PageCache::class,
        'performance' => PerformanceMetrics::class,
        'toolbar' => DebugToolbar::class,
    ];
    public array $globals = [
        'before' => [
            'csrf',
        ],
    ];
    public array $methods = [];
    public array $filters = [];
}
