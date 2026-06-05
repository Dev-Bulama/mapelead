<?php
namespace App\Services\CMS;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SettingsService
{
    public function all(): array
    {
        return SiteSetting::all()->groupBy('group')->map(fn($items) =>
            $items->pluck('value', 'key')
        )->toArray();
    }

    public function group(string $group): array
    {
        return SiteSetting::getGroup($group);
    }

    public function get(string $key, $default = null): mixed
    {
        return SiteSetting::get($key, $default);
    }

    public function updateGroup(string $group, array $data): void
    {
        foreach ($data as $key => $value) {
            if (str_starts_with($key, '_') || $key === 'group') continue;
            SiteSetting::set($key, $value);
        }
        // Clear individual and group caches without tags (compatible with database cache driver)
        Cache::forget("settings_group_{$group}");
        // Also bust all known setting keys in this group
        $keys = SiteSetting::where('group', $group)->pluck('key');
        foreach ($keys as $key) {
            Cache::forget("setting_{$key}");
        }
    }
}
