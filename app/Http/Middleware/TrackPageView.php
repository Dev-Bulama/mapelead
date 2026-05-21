<?php
namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageView
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('GET') && !$request->is('admin/*') && !$request->expectsJson()) {
            try {
                PageView::create([
                    'url'        => $request->path(),
                    'title'      => null,
                    'user_id'    => auth()->id(),
                    'session_id' => $request->session()->getId(),
                    'ip_address' => $request->ip(),
                    'device'     => $this->getDevice($request->userAgent()),
                    'browser'    => $this->getBrowser($request->userAgent()),
                    'referrer'   => $request->header('referer'),
                ]);
            } catch (\Exception $e) {
                // Silently fail — analytics should never break the app
            }
        }

        return $response;
    }

    private function getDevice(?string $ua): string
    {
        if (!$ua) return 'unknown';
        if (str_contains($ua, 'Mobile')) return 'mobile';
        if (str_contains($ua, 'Tablet')) return 'tablet';
        return 'desktop';
    }

    private function getBrowser(?string $ua): string
    {
        if (!$ua) return 'unknown';
        foreach (['Chrome', 'Firefox', 'Safari', 'Edge', 'Opera'] as $browser) {
            if (str_contains($ua, $browser)) return $browser;
        }
        return 'other';
    }
}
