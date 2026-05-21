<?php
namespace App\Http\Middleware;

use App\Models\Redirect as RedirectModel;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        $path = '/' . ltrim($request->path(), '/');
        $redirect = RedirectModel::where('from_path', $path)->where('is_active', true)->first();

        if ($redirect) {
            return redirect($redirect->to_path, $redirect->status_code);
        }

        return $next($request);
    }
}
