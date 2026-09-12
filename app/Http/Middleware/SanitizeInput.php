<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    // Fields that may legitimately contain HTML (e.g. rich-text editor content)
    protected array $except = ['body', 'content', 'description', 'long_description', 'message'];

    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        $request->merge($this->sanitize($input));
        return $next($request);
    }

    private function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (in_array($key, $this->except, true)) continue;

            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (is_string($value)) {
                $data[$key] = strip_tags(trim($value));
            }
        }
        return $data;
    }
}
