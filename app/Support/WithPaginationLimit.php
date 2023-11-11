<?php

namespace App\Support;

use Illuminate\Config\Repository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

trait WithPaginationLimit
{
    /**
     * Get pagination limit from the request data or default to the default limit.
     *
     * @param Request $request
     * @param string $key
     * @param integer|null $default
     * @return Repository|int|mixed
     */
    public function getPaginationLimit(Request $request, string $key = 'limit', int $default = null): mixed
    {
        $default = $default ?? config('api.per_page', 50);

        $limit = ($request->filled($key) && is_numeric($request->get($key))) ? (int)$request->get($key) : $default;

        if ($limit > 50) $limit = 50;

        return $limit;
    }
}
