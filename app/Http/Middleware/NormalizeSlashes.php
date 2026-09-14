<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class NormalizeSlashes
{
    public function handle(Request $request, Closure $next)
    {
        $uri = $request->getRequestUri();
        if (str_contains($uri, '//')) {
            $normalized = preg_replace('#/{2,}#', '/', $uri);
            return redirect($normalized, 301);
        }
        return $next($request);
    }
}
